// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: driver.js,v 1.1.4.5 2025/04/29 09:16:47 qvarin Exp $

let nedap_debug_active = false;
const constants = {
  TAG_FULL_TYPE_MEMBER: "ADHERENT",
  TAG_FULL_TYPE_DOCUMENT: "DOCUMENT",
  TAG_TYPE_MEMBER: "ADH",
  TAG_TYPE_DOCUMENT: "DOC",

  FREQUENCY_HF: "HF",
  FREQUENCY_UHF: "UHF",
};

function nedap_debug(msg) {
  if (true === nedap_debug_active) {
    console.debug(msg)
  }
}

class NedapWebSocket extends EventTarget {

  static get ACK_OPERATION() {
    return {
      "setMode": "read_event",
      "writeDoc": {op: "read_event", count: 3},
    };
  }

  /**
   * Constructeur
   *
   * @param {string} socket
   * @param {string} token
   * @param {string} rcr
   * @param {string} station
   */
  constructor(socket, token, rcr, station) {
    super();

    this.connectionName = "nedap-pmbservices";
    this.token = token;
    this.rcr = rcr;
    this.station = station;
    this.tagFrequency = null;

    this.ws = new WebSocket(socket);
    this.connect();
  }

  /**
   * Connecte au serveur
   */
  connect() {
    this.ws.onopen = async () => {
      nedap_debug("Connection open.");

      const responseData = await this.sendLoginRequest();
      if (responseData) {
        this.saveClientInformations(responseData);

        const bibli = responseData.data.STATIONS.find(b => b.RCR === this.rcr);
        if (!bibli) {
          throw new Error("Unknown RCR: " + this.rcr);
        }

        const station = bibli.STATIONS.find(s => s.HOSTNAME === this.station);
        if (!station) {
          throw new Error("Unknown station: " + this.station);
        }

        const typeEncodage = station?.type_encodage || null;
        if (!typeEncodage) {
          throw new Error("Unknown type_encodage: " + typeEncodage);
        }

        // Type d'encodage -> Frequence
        // - UHF 128 : UHF
        // - HF FR01 : HF
        if (typeEncodage === "HF FR01") {
          this.tagFrequency = constants.FREQUENCY_HF;
        } else if (typeEncodage === "UHF 128") {
          this.tagFrequency = constants.FREQUENCY_UHF;
        } else {
          alert("Type d'encodage inconnu: " + typeEncodage);
          throw new Error("Unknown type_encodage: " + typeEncodage);
        }

        await this.sendModeRequest('NORMAL');
      }
    };

    this.ws.onmessage = (event) => { this.processReceivedData(event.data); };
    this.ws.onerror = (error) => { console.error("Error: " + JSON.stringify(error)); };
    this.ws.onclose = () => { nedap_debug("Connection closed."); };
  }

  /**
   * Envoie des données
   *
   * @param {object} data
   * @param {Promise|null} promise
   * @returns {Promise}
   */
  async sendData(data, promise = null) {
    // Common parameters
    data.type = "SIGB";
    data.token = this.token;
    data.connectionName = this.connectionName;
    data.rcr = this.rcr;

    if (this.ws && this.ws.readyState === this.ws.OPEN) {
      try {
        nedap_debug("Sending data: " + JSON.stringify(data));
        this.ws.send(JSON.stringify(data));
      } catch (ex) {
        console.error("Error while sending data: " + ex.message);
      }
    } else {
      console.error("Error: websocket connection is closed or not available.");
    }

    if (promise) {
      return promise;
    }

    return new Promise((resolve, reject) => {
      if (data.operation in NedapWebSocket.ACK_OPERATION) {
        this.addEventListener("response-" + NedapWebSocket.ACK_OPERATION[data.operation], (event) => { resolve(event.detail); }, { once: true });
      } else {
        this.addEventListener("response-" + data.operation, (event) => { resolve(event.detail); }, { once: true });
      }
      this.addEventListener("response-error", (event) => { reject(event.detail); }, { once: true });
    })
  }

  /**
   * Traitement des données reçues
   *
   * @param {string} receivedData
   * @returns {void}
   */
  processReceivedData(receivedData) {
    if (!receivedData) {
      // Message vide reçu, rien à faire.
      return;
    }


    let responseData;
    try {
      responseData = JSON.parse(receivedData);
    } catch (ex) {
      this.dispatchEvent(new CustomEvent("response-error", { detail: ex }))
      return;
    }

    switch (responseData.operation) {
      case "login_information":
        this.dispatchEvent(new CustomEvent('response-login', { detail: responseData }));
        break;

      case "easinfo":
        let event = responseData.statusEAS.actioneas.toLowerCase() + "eas";
        this.dispatchEvent(new CustomEvent('response-' + event, { detail: responseData }));
        break;

      default:
        if (responseData.ack) {
          // On est sur un aquittement
          this.dispatchEvent(new CustomEvent('response-' + responseData.message.operation, { detail: responseData }));
        } else {
          this.dispatchEvent(new CustomEvent('response-' + responseData.operation, { detail: responseData }));
        }
        break;
    }
  }

  /**
   * Permet de s'authentifier sur le serveur
   *
   * @returns {Promise}
   */
  sendLoginRequest() {
    return this.sendData({ operation: "login" });
  }

  /**
   * Permet d'activer l'antivol
   *
   * @param {string} identifiant
   * @returns {Promise}
   */
  sendSetEasRequest(identifiant) {
    return this.sendData({
      operation: "seteas",
      identifiant: identifiant,
      station: this.station,
    });
  }

  /**
   * Permet de desactiver l'antivol
   *
   * @param {string} identifiant
   * @returns {Promise}
   */
  sendResetEasRequest(identifiant) {
    return this.sendData({
      operation: "reseteas",
      identifiant: identifiant,
      station: this.station,
    });
  }

  /**
   * Permet de changer le mode
   *
   * @param {string} mode
   * @returns {Promise}
   */
  sendModeRequest(mode) {
    return this.sendData({
      operation: "setMode",
      mode: mode,
      station: this.station,
    });
  }

  /**
   * Permet d'écrire une puce
   *
   * @param {string} identifiant
   * @param {string} typeDoc
   * @param {number} nbPart
   * @returns {Promise}
   */
  sendWriteDoc(identifiant, typeDoc, nbPart = 1) {
    const promise = new Promise((resolve, reject) => {
      let countReadEvent = 0;
      let functionEventResponse = (event) => {
        // On attend 5 read_event avant de considerer que l'operation est finie
        if (countReadEvent >= 5) {
          resolve(event.detail);

          // On évite de rester en ecoute
          this.removeEventListener("response-read_event", functionEventResponse);
        } else {
          countReadEvent++;
        }
      };

      this.addEventListener("response-read_event", functionEventResponse);
      this.addEventListener("response-error", (event) => { reject(event.detail); }, { once: true });
    })

    return this.sendData({
      operation: "writeDoc",
      station: this.station,
      identifier: identifiant,
      items: nbPart,
      typeDoc: typeDoc,
      typeMedia: this.tagFrequency === constants.FREQUENCY_HF ? "PAPER" : "Normal",
    }, promise);
  }

  saveClientInformations(responseData) {
    let accountAvailability = new Date(responseData.data.ACCOUNT_AVAILABILITY);
    let accountUsage = responseData.data.ACCOUNT_USAGE;
    this.clientInformations = {
      'Disponibilité du compte' : `${accountAvailability.getFullYear()}-${accountAvailability.getMonth() + 1}-${accountAvailability.getDate()}`,
      'Utilisation aujourd\'hui': accountUsage?.TODAY || null,
      'Utilisation ce mois-ci': accountUsage?.THISMONTH || null,
      'Utilisation disponible pour aujourd\'hui': accountUsage?.AVAILABLE_TODAY || null,
      'Utilisation disponible pour ce mois-ci': accountUsage?.AVAILABLE_THISMONTH || null,
    }

    let stations = responseData.data.STATIONS;
    this.clientInformations['biblio'] = [];
    if (stations) {
      stations.forEach(station => {
        this.clientInformations['biblio'].push({
          'rcr': station.RCR,
          'stations': station.STATIONS
        })
      });
    }
  }
}

class Collection {
  constructor() {
    this.items = {};
  }

  add(key, item) {
    this.items[key] = item;
  }

  remove(key) {
    delete this.items[key];
  }

  has(key) {
    return key in this.items;
  }

  clear() {
    this.items = {};
  }

  get size() {
    let size = 0;

    for (const key in this.items) {
      const tags = this.items[key];
      size += tags.length ;
    }

    return size;
  }

  toArray() {
    return Object.values(this.items);
  }
}

class Nedap {

  constructor(config) {
    this.items = new Collection();
    this.invalidItemDetected = false;
    this.lastReceivedTimestamp = null;

    const [socket, token, rcr, station] = config.split(',');
    this.ws = new NedapWebSocket(socket, token, rcr, station);
    this.ws.addEventListener('response-read_event', this.updateTags.bind(this));

    this.cleanInterval = setInterval(() => {
      if (this.lastReceivedTimestamp !== null && Date.now() - this.lastReceivedTimestamp > 1000) {
        // Réinitialiser l'ensemble des tags uniques
        this.items.clear();
      }
    }, 100);

    this.invalidItemInterval = setInterval(() => {
      if (this.invalidItemDetected) {
        alert("Étiquettes non compatibles avec la platine !");
        this.invalidItemDetected = false;
      }
    }, 1000);
  }

  /**
   * Permet de mettre à jour la liste des tags
   *
   * @param {CustomEvent} event
   */
  updateTags(event) {
    this.lastReceivedTimestamp = Date.now();

    this.items.clear();
    event.detail.read.forEach((tag) => {
      const AllFreq = tag.AvailableTags.map((tag) => tag.Freq);
      const OtherFreq = AllFreq.find(f => f !== this.ws.tagFrequency);
      if (OtherFreq) {
        this.invalidItemDetected = true;
      } else {
        this.items.add(tag.Identifiant, tag.AvailableTags);
      }
    });
  }

  /**
   * Permet d'afficher les informations du client
   */
  printClientInformations() {
    console.table(this.ws.clientInformations);
  }

  /**
   * Permet de récupérer la liste des items
   *
   * @param {boolean} multiple
   * @param {boolean} formated
   */
  getItems(multiple = false, formated = true) {
    let result = null;
    if (formated) {
      result = this.formatItems(multiple);
    } else {
      let items = this.items.toArray();
      const length = multiple ? items.length : 1;
      result = items.slice(0, length);
    }

    return Promise.resolve(result)
  }

  /**
   * Permet de modifier la sécurité d'un tag
   *
   * @param {string} idTag
   * @param {boolean} IsSecured
   */
  async setTagSecurity(IsSecured, idTag) {
    if (this.ws.tagFrequency === constants.FREQUENCY_UHF) {
      // Les puces UHF n'ont pas d'antivol
      console.info('[setTagSecurity] UHF tag not available for security');
      return true;
    }

    let responseData;
    if (IsSecured) {
      responseData = await this.ws.sendSetEasRequest(idTag);
    } else {
      responseData = await this.ws.sendResetEasRequest(idTag);
    }

    if (responseData.ack) {
      return true;
    }
    return responseData["status"] === "SUCCESS";
  }


  /**
   * Permet de vider un tag
   *
   * @param {string} tagId
   */
  async clearTag(tagId) {
    // Pas possible avec le websocket (aucune commande)
    return true;
  }

  /**
   * Permet d'ecrire une puce pour un exemplaire
   *
   * @param {string} cb
   * @param {integer} nbPart
   */
  async writeExpl(cb, nbPart = 1) {
    if (!cb) {
      console.error("[Nedap - writeExpl] Wrong cb !");
      return false;
    }

    if (this.items.size != nbPart) {
      alert("Le nombre d'étiquette ne correspond pas !");
      console.error("[Nedap - writeExpl] Wrong part number !");
      return false;
    }

    const result = await this.ws.sendWriteDoc(cb, constants.TAG_TYPE_DOCUMENT, nbPart);
    return (
      result.read[0].Identifiant === cb &&
      result.read[0].TotalTagsNumber == nbPart
    )
  }

  /**
   * Permet d'ecrire une puce pour un emprunteur
   *
   * @param {string} cb
   */
  async writeEmpr(cb) {
    if (!cb) {
      console.error("[Nedap - writeEmpr] Wrong cb !");
      return false;
    }

    if (this.items.size != 1) {
      alert("Le nombre d'étiquette ne correspond pas !");
      console.error("[Nedap - writeEmpr] Wrong part number !");
      return false;
    }

    const result = await this.ws.sendWriteDoc(cb, constants.TAG_TYPE_MEMBER);
    if (result.read[0].Identifiant === cb) {
      return this.setTagSecurity(false, cb);
    }
    return false;
  }


  /**
   * Permet de formater les items
   *
   * @param {boolean} multiple
   * @return {object}
   */
  formatItems(multiple = false) {
    const formatedItems = { empr: [], expl: [] };
    if (this.items.size > 0) {
      for (const i in this.items.items) {
        const tags = this.items.items[i];
        for (const tag of tags) {
          if (constants.TAG_FULL_TYPE_MEMBER == tag.Type) {
            formatedItems.empr.push(tag.Identifiant);
          } else {
            formatedItems.expl.push({
              cb: tag.Identifiant,
              tagId: tag.Identifiant,
              IsSecured: tag.antivol == "ON",
              IsValid: tag.IsComplete,
              part: tag.ElementNumber,
              nbPart: tag.TotalElements,
            });
          }
        }

        if (!multiple) {
          break;
        }
      }
    }

    return formatedItems;
  }

  /**
   * Permet de savoir si on est sur la page des retour de document
   *
   * @returns {boolean}
   */
  isReturnExplPage() {
    return window.location.href.toLowerCase().includes('categ=retour') ? true : false;
  }

  /**
   * Permet de savoir si on est sur la page de lecture rfid
   *
   * @returns {boolean}
   */
  isRFIDReadPage() {
    return window.location.href.toLowerCase().includes('categ=rfid_read') ? true : false;
  }

  /**
   * Permet de savoir si on est sur la page d'edition ou de creation d'un exemplaire
   *
   * @returns {boolean}
   */
  isEditExplPage() {
    return (
      window.location.href.toLowerCase().includes('categ=edit_expl') ||
      window.location.href.toLowerCase().includes('categ=expl_create') ||
      window.location.href.toLowerCase().includes('action=expl_form')
    ) ? true : false;
  }
}
