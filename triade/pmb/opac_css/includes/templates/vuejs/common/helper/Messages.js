class Messages {
  get(js, code) {
    if (code.slice(0, 4) == "msg:") {
      code = code.slice(4);
    }

    const message = pmbDojo.messages.getMessage(js, code);
    return "" != message ? message : code;
  }
}

export default new Messages();
