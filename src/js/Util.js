class Util {
  static copyToClipBoad(copyText, event = null) {
    copyText = Util.replaceAll(copyText, '&lt;', '<');
    copyText = Util.replaceAll(copyText, '&gt;', '>');
    copyText = Util.replaceAll(copyText, '&amp;', '&');
    copyText = Util.replaceAll(copyText, '<br>', '\n');
    copyText = Util.replaceAll(copyText, '@```', '```');
  
    const textarea = document.createElement("textarea");
    textarea.textContent = copyText;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);
    if (!event) {
      return;
    }
    event();
  }
  static replaceAll(str, beforeStr, afterStr){
    let reg = new RegExp(beforeStr, "g");
    return str.replace(reg, afterStr);
  }
}
