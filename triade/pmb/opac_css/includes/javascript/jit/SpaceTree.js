class SpaceTree {

    /**
     * Correspond a une instance de space tree
     *
     * @see SpaceTree.instance
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Visualizations/Spacetree-js.html
     */
    _st = null;

    /**
     * Indicateur ajoute au label contenant des enfants si le noeud est replie
     */
    childIndicator = " *";

    /**
     * Couleur des noeuds selectionnes
     */
    selectedColorNode = "#ff7";

    /**
     * Couleur des lignes selectionnees
     */
    selectedColorLine = "#eed";

    /**
     * URL de l'api permettant de recuperer les enfants.
     */
    api;

    /**
     * Permet de savoir sur quel noeud nous sommes allés chercher les enfants
     */
    childrenFetch = [];

    /**
     * Permet de savoir si on peut redimensionner le canvas
     */
    allowResize = true;

    /**
     * Permet de savoir si on etend le graph
     */
    expand = false;

    /**
     * Contient les options pour calculer le redimensionner le canvas
     */
    originPosition = {
        y: 0,
        height: 0,
        minHeight: 200,
    }

    /**
     * Taille des lignes selectionnees
     */
    selectedWidthLine = 3;

    /**
     * Options par defaut du Space Tree
     */
    defaultOptions = {
        duration: 800,
        transition: $jit.Trans.Quart.easeInOut,
        levelDistance: 100,
        levelsToShow: 2,
        Navigation: {
            enable: true,
            panning: true,
            zooming: 100
        },
        Tree: {
            align: 'center'
        },
        Node: {
            height: 40,
            width: 100,
            dim: 100,
            type: 'rectangle',
            color: '#aaa',
            overridable: true
        },
        Label: {
            style: "",
            size: 10,
            color: "#fff",
            overridable: true
        },
        Edge: {
            type: 'bezier',
            overridable: true
        }
    };

    /**
     * Retourne l'instance infovis (Space Tree)
     * @returns {Object}
     */
    get instance() {
        return this._st;
    }

    /**
     * Permet de definir l'instance infovis (Space Tree)
     */
    set instance(newValue) {
        this._st = newValue;
    }

    /**
     * Retourne s'instance du graph
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Graph/Graph-js.html
     */
    get graph() {
        return this.instance.graph ?? null;
    }

    /**
     * Retourne la config de l'instance
     */
    get config() {
        return this.instance.config ?? null;
    }

    /**
     * Permet de redefinir la config de l'instance
     */
    set config(newValue) {
        this.instance.config = newValue;
    }

    /**
     * Retourne le container qui contient le canvas
     * @returns {Node}
     */
    get container() {
        return document.getElementById(this.config.injectInto);
    }

    /**
     * Retourne le canvas
     * @returns {Node}
     */
    get canvas() {
        return document.getElementById(`${this.config.injectInto}-canvas`);
    }

    /**
     * Retourne la liste des evenements que l'on peut ajoute
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Options/Options-Controller-js.html
     */
    get eventList() {
        return [
            'onBeforeCompute',
            'onAfterCompute',
            'onCreateLabel',
            'onPlaceLabel',
            'onBeforePlotNode',
            'onAfterPlotNode',
            'onBeforePlotLine',
            'onAfterPlotLine'
        ];
    }

    /**
     * Permet de creer un graph Space Tree
     *
     * @param {string} idContainer
     * @param {object} options
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Visualizations/Spacetree-js.html
     */
    constructor(idContainer, options = null) {
        options = options || this.defaultOptions;
        options['injectInto'] = idContainer;
        options.Node.type = options.Node.type ?? "rectangle";
        if (["square", "circle"].includes(options.Node.type)) {
            options.Node.width = options.Node.dim;
            options.Node.height = options.Node.dim;
        }

        this.instance = new $jit.ST(options);

        if (!window.infovis) {
            window.infovis = {};
        }
        window.infovis[idContainer] = this;
    }

    /**
     * Charge une structure pour le rendu
     *
     * @param {object} data
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Loader/Loader-js.html#Loader.loadJSON
     */
    load(data) {
        this.instance.loadJSON(data);
    }

    /**
     * Permet de faire le rendu du graph
     *
     * @param {string} currentId
     */
    render(currentId = null) {
        currentId = currentId ?? this.instance.root;

        this.instance.compute();
        this.instance.geom.translate(new $jit.Complex(-200, 0), "current");

        if (!this.exist(currentId)) {
            currentId = this.instance.root;
        }

        if (this.allowResize) {
            this.activeResize();
        }

        if (this.config.Navigation.zooming) {
            this.activeZooming();
        }


        this.goTo(currentId);
    }

    /**
     * Attache une fonction a appeler chaque fois que l'evenement specifie est envoye
     *
     * @param {string} event
     * @param {function} callback
     */
    addEventListener(event, callback) {
        if (typeof callback !== "function") {
            throw new Error('callback is not a function');
        }
        if (!this.eventList.includes(event)) {
            throw new Error('Event unknown');
        }

        this.config[event] = callback.bind(this);
    }

    /**
     * Permet d'ajouter un sous arbre
     *
     * @param {string} idParent
     * @param {object} children
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Visualizations/Spacetree-js.html#ST.addSubtree
     */
    addChildren(idParent, children = []) {
        if (!this.exist(idParent)) {
            throw new Error('parent id does not exist');
        }

        const subtree = {
            id: idParent,
            children
        };

        return this.instance.addSubtree(subtree, "replot");
    }

    /**
     * Permet de supprimer un sous arbre
     *
     * @param {string} idNode
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Visualizations/Spacetree-js.html#ST.removeSubtree
     */
    remove(idNode) {
        if (!this.exist(nodeId)) {
            throw new Error('Node id does not exist');
        }

        this.instance.removeSubtree(idNode, true, 'replot');
    }

    /**
     * Permet de supprimer les enfants d'un noeud
     *
     * @param {string} idNode
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Visualizations/Spacetree-js.html#ST.removeSubtree
     */
    removeChildren(idNode) {
        if (!this.exist(nodeId)) {
            throw new Error('Node id does not exist');
        }

        this.instance.removeSubtree(idNode, false, 'replot');
    }

    /**
     * Permet de centrer un noeud donne
     *
     * @param {string} nodeId
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Visualizations/Spacetree-js.html#ST.onClick
     */
    goTo(nodeId) {
        if (this.exist(nodeId)) {
            this.instance.onClick(nodeId, {
                Move: {
                    enable: true,
                    offsetX: this.instance.canvas.translateOffsetX,
                    offsetY: this.instance.canvas.translateOffsetY
                }
            });
        }
    }

    /**
     * Permet de recuperer un noeud present dans le graph
     *
     * @param {string} nodeId
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Graph/Graph-js.html#Graph.Util.getNode
     */
    get(nodeId) {
        return this.exist(nodeId) ?
            this.graph.getNode(nodeId) :
            null;
    }

    /**
     * Permet de savoir si un noeud exist
     * @param {string} nodeId
     * @returns boolean
     * @see https://philogb.github.io/jit/static/v20/Docs/files/Graph/Graph-js.html#Graph.hasNode
     */
    exist(nodeId) {
        return this.graph.hasNode(nodeId) === true;
    }

    /**
     * Permet de faire un instance avec le option par defaut
     *
     * @param {string} idContainer
     */
    static makeInstance(idContainer) {
        const spacetree = new SpaceTree(idContainer);

        spacetree.addEventListener("onCreateLabel", function (container, node) {

            // Label
            const idLabel = "label-" + node.id;
            const label = document.createElement('p');
            label.id = idLabel;
            label.innerText = node.name;
            label.classList.add('label');
            label.style.fontSize = this.config.Label.size + 'px';
            label.style.color = this.config.Label.color;
            if (this.config.Label.style == 'bold') {
                label.style.fontWeight = this.config.Label.style;
            } else {
                label.style.fontStyle = this.config.Label.style;
            }

            // Container label
            const containerLabel = document.createElement('div');
            containerLabel.appendChild(label);
            containerLabel.classList.add('container-label');

            // Container Tooltip
            let tooltip = false;
            if (node.data.tooltip) {
                tooltip = document.createElement('div');
                tooltip.classList.add('container-tooltip')
                tooltip.style.display = "none";
                tooltip.innerHTML = node.data.tooltip;
                tooltip.onclick = (event) => { event.stopPropagation(); };
                tooltip.onmousemove = (event) => { event.stopPropagation(); };
            }

            // Container
            container.id = node.id;
            container.setAttribute('aria-labelledby', idLabel);
            container.setAttribute('data-node-type', this.config.Node.type);

            // Container set event
            const functionClick = async (event) => {
                await this.fetchChildren(node.id, {
                    onComplete: () => {
                        this.goTo(node.id);
                    }
                });
            };
            convertToRGAAButton(container, functionClick);
            container.addEventListener('click', functionClick);
            container.addEventListener('mouseenter', (event) => {
                if (tooltip && event.target.id === container.id) {
                    tooltip.style.display = "block";
                }
            });
            container.addEventListener('mouseleave', (event) => {
                if (tooltip) {
                    tooltip.style.display = "none";
                }
            });

            // Container set styles
            let style = container.style;
            style.cursor = 'pointer';

            const size = this.computeSizeNode(node);
            style.width = size.width + 'px';
            style.height = size.height + 'px';


            container.appendChild(containerLabel);
            if (tooltip) {
                container.appendChild(tooltip);
            }
        });

        spacetree.addEventListener("onBeforePlotNode", function (node) {
            if (node.selected) {
                node.data.$color = this.selectedColorNode;
            } else {
                node.data.$color = this.config.Node.color;
            }

            if (node.selected && node.data.hasChildIndicator) {
                this.changeLabel(node.id, node.name.slice(0, -2))
            } else if (!node.selected && !node.data.hasChildIndicator && node.data.hasChildren) {
                this.changeLabel(node.id, node.name + this.childIndicator)
            }

        });

        spacetree.addEventListener("onBeforePlotLine", function (line) {
            if (line.nodeFrom.selected && line.nodeTo.selected) {
                line.data.$color = this.selectedColorLine;
                line.data.$lineWidth = this.selectedWidthLine;
            } else {
                delete line.data.$color;
                delete line.data.$lineWidth;
            }
        });
        spacetree.addEventListener("onPlaceLabel", function (domElement, node) {
            if (!this.config) {
                return false;
            }

            if (!this.config.Node.widthOrigine) {
                this.config.Node.widthOrigine = parseInt(this.config.Node.width);
            }
            if (!this.config.Node.heightOrigine) {
                this.config.Node.heightOrigine = parseInt(this.config.Node.height);
            }

            const size = this.computeSizeNode(node);
            node.setData('width', size.width);
            node.setData('height', size.height);
            domElement.style.width = size.width + "px";
            domElement.style.height = size.height + "px";

            this.instance.labels.placeLabel(domElement, node, {
                onPlaceLabel: () => {
                    node.setData('width', this.config.Node.widthOrigine);
                    node.setData('height', this.config.Node.heightOrigine);
                }
            });
        });

        return spacetree;
    }

    /**
     * Permet de definir les options fournies par un cadre de portail
     *
     * @param {object} options
     */
    setCMSOption(options) {

        if (options['node_color_selected']) {
            this.selectedColorNode = options['node_color_selected'];
        }

        if (options['distance']) {
            this.config.levelDistance = parseInt(options['distance']) ?? 50;
        }

        if (options['levels_to_show']) {
            this.config.levelsToShow = parseInt(options['levels_to_show']) ?? 2;
        }

        if (options['node_type']) {
            this.config.Node.type = options['node_type'];
        }
        if (options['node_width']) {
            this.config.Node.width = options['node_width'];
            this.config.Node.dim = options['node_width'];
        }
        if (options['node_height']) {
            this.config.Node.height = options['node_height'];
        }
        if (["square", "circle"].includes(this.config.Node.type)) {
            this.config.Node.width = this.config.Node.dim;
            this.config.Node.height = this.config.Node.dim;
        }
        if (options['node_color']) {
            this.config.Node.color = options['node_color'];
        }

        if (options['label_style']) {
            this.config.Label.style = options['label_style'];
        }
        if (options['label_size']) {
            this.config.Label.size = parseInt(options['label_size']);
        }
        if (options['label_color']) {
            this.config.Label.color = options['label_color'];
        }
        if (options['api']) {
            this.api = options['api'];
        }
    }

    /**
     * Permet d'aller chercher les noeuds enfants d'un point via une API
     *
     * @param {string} nodeId
     * @returns {boolean}
     */
    async fetchChildren(nodeId, onComplete = null) {

        if (!onComplete.onComplete) {
            onComplete.onComplete = () => { };
        }

        if (!this.api) {
            onComplete.onComplete();
            return false;
        }

        if (this.childrenFetch.includes(nodeId)) {
            onComplete.onComplete();
            return false;
        }

        let result = [];
        this.childrenFetch.push(nodeId)

        const node = this.graph.getNode(nodeId);

        let post = new URLSearchParams();
        post.append("entity_id", node.id);
        post.append("entity_type", node.data.entity_type);

        try {
            const response = await fetch(this.api, {
                method: "POST",
                body: post,
                cache: 'no-cache'
            });
            result = await response.json();
        } catch (e) {
            return false;
        }

        let parent = null;
        if (result.tree.id == result.current) {
            parent = this.graph.getNode(result.current);
        }
        this.createNode(result.tree, parent, onComplete);
        return result.current;
    }

    /**
     * Permet de creer un noeud dans le graph
     *
     * @param {Object} node
     * @param {Object} parent
     * @param {Object} onComplete
     */
    createNode(node, parent = null, onComplete = null) {

        let newNode = false;
        if (!this.graph.hasNode(node.id)) {
            newNode = true;
            this.graph.addNode(node);
            this.instance.refresh();
        }

        this.addChildren(node.id, node.children);

        if (newNode && !parent) {
            this.instance.setRoot(node.id, 'replot', {
                onComplete: () => {
                    this.instance.refresh()
                    onComplete.onComplete();
                }
            });
        } else {
            onComplete.onComplete();
        }
    }

    /**
     * Permet de changer le label d'un noeud
     *
     * @param {string} nodeId
     * @param {string} newLabel
     * @returns {boolean}
     */
    changeLabel(nodeId, newLabel) {

        if (!this.graph.hasNode(nodeId)) {
            return false;
        }

        let label = document.getElementById("label-" + nodeId);
        if (label) {
            label.innerText = newLabel;
        }

        const indicateurLength = this.childIndicator.length - (this.childIndicator.length * 2);

        let node = this.graph.getNode(nodeId);
        node.name = newLabel;
        node.data.hasChildIndicator = node.name.slice(indicateurLength) == this.childIndicator;
        return true;
    }

    computeSizeNode(node) {
        let size = { width: 0, height: 0 };

        switch (this.config.Node.type) {
            case "square":
            case "circle":
                size.width = node.Node.dim * this.instance.canvas.scaleOffsetX;
                size.height = node.Node.dim * this.instance.canvas.scaleOffsetX;
                break;

            default:
                size.width = node.Node.width * this.instance.canvas.scaleOffsetX;
                size.height = node.Node.height * this.instance.canvas.scaleOffsetX;
                break;
        }

        return size;
    }


    activeResize() {
        const boundingClientRect = this.container.getBoundingClientRect()
        this.originPosition.minHeight = boundingClientRect.height;

        const resizeImg = document.createElement('img');
        resizeImg.setAttribute('src', pmbDojo.images.getImage('expand-arrows.png'));
        resizeImg.setAttribute('alt', pmbDojo.messages.getMessage('graph', 'infovis_resize'));
        resizeImg.setAttribute('title', pmbDojo.messages.getMessage('graph', 'infovis_resize'));
        resizeImg.classList.add('infovis-resize-icon');

        const resizeButton = document.createElement('button');
        resizeButton.setAttribute('type', 'button');
        resizeButton.setAttribute('data-pressed', 'false');
        resizeButton.classList.add('infovis-resize-btn', 'infovis-expand');

        resizeButton.addEventListener('mousedown', (event) => {
            event.preventDefault();
            const currentHeight = parseInt(this.canvas.getAttribute('height'));

            this.expand = true;
            this.originPosition.y = event.clientY;
            this.originPosition.height = currentHeight;
            document.body.classList.add('infovis-expand');
        });
        window.addEventListener('mouseup', (event) => {
            event.preventDefault();

            this.expand = false;
            this.originPosition.y = 0;
            this.originPosition.height = 0;
            document.body.classList.remove('infovis-expand');
        })
        window.addEventListener('mousemove', (event) => {
            if (this.expand) {
                const currentHeight = parseInt(this.canvas.getAttribute('height'));
                const currentWidth = parseInt(this.canvas.getAttribute('width'));

                const height = this.originPosition.height + (event.clientY - this.originPosition.y)
                const diff = (height - currentHeight);
                const new_height = currentHeight + diff;

                if (this.originPosition.minHeight < new_height) {
                    this.instance.canvas.resize(currentWidth, new_height);
                } else {
                    this.instance.canvas.resize(currentWidth, this.originPosition.minHeight);
                }
            }
        });

        resizeButton.appendChild(resizeImg);
        this.container.appendChild(resizeButton)
    }

    activeZooming() {
        const zoomInButton = document.createElement('button');
        zoomInButton.setAttribute('type', 'button');
        zoomInButton.setAttribute('title', pmbDojo.messages.getMessage('graph', 'zoom_in_button'));
        zoomInButton.classList.add('infovis-zoom-in-btn', 'infovis-zoom');


        const zoomInIcon = document.createElement('span');
        zoomInIcon.setAttribute('aria-label', pmbDojo.messages.getMessage('graph', 'zoom_in_button'));
        zoomInIcon.innerText = "+";
        zoomInButton.appendChild(zoomInIcon);

        const zoomOutButton = document.createElement('button');
        zoomOutButton.setAttribute('type', 'button');
        zoomOutButton.setAttribute('title', pmbDojo.messages.getMessage('graph', 'zoom_out_button'));
        zoomOutButton.classList.add('infovis-zoom-out-btn', 'infovis-zoom');

        const zoomOutIcon = document.createElement('span');
        zoomOutIcon.setAttribute('aria-label', pmbDojo.messages.getMessage('graph', 'zoom_out_button'));
        zoomOutIcon.innerText = "-";
        zoomOutButton.appendChild(zoomOutIcon);

        zoomInButton.addEventListener('click', (event) => {
            event.preventDefault();

            this.instance.canvas.scale(1.25, 1.25, true);
        });
        zoomOutButton.addEventListener('click', (event) => {
            event.preventDefault();

            this.instance.canvas.scale(0.75, 0.75, true);
        });

        const zoomContainer = document.createElement('div');
        zoomContainer.classList.add('infovis-zoom-container');
        zoomContainer.appendChild(zoomInButton);
        zoomContainer.appendChild(zoomOutButton);

        this.container.appendChild(zoomContainer);
    }
}