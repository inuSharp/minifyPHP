class Tab {

  static #tabs = ['tab_memo', 'tab_db'];

  static select(elm, id) {
    for (const tabContent of this.#tabs) {
      document.getElementById(tabContent).style.display='none';
    }
    document.getElementById(id).style.display='';

    for (const tab of document.getElementsByClassName('tab')) {
      tab.classList.remove('is_active');
    }
    elm.classList.add('is_active');

    this.initTab(id);
  }

  static initTab(id) {
    switch (id) {
      case 'tab_memo':
        Memo.fetchIndexes();
        break;
      case 'tab_db':
        DB.open();
        break;
    }

  }
}
