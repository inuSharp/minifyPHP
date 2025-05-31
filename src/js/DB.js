class DB {
  static indexGroup = [];
  static isOpen = false;
  static selectedDB = '';

  static async clearCheck() {
  }

  // タブを開いた最初の時にDBListを取得する
  static async open() {
    if (DB.isOpen) {
      return;
    }
    await DB.fetchIndexes();
    DB.isOpen = true;
  }

  static async fetchIndexes() {
    const data = await Api.fetchDBIndex();
    console.log(data.result);
    DB.makeIndexTag(data.result);
  }

  static makeIndexTag(names) {
    let html = '';
    let word = '';
    for (const name of names) {
      html += '<label><input name="db_name_radio" type="radio" value="' + name + '" onchange="DB.changeDB()"> ' + name + '</label><br>';
    }
    document.getElementById('db_index').innerHTML = html;
  }

  static makeTableNameTag(names) {
    let html = '';
    let word = '';
    for (const name of names) {
      html += '<label><input name="table_name_radio" type="radio" value="' + name + '" onchange="DB.changeTable()"> ' + name + '</label><br>';
    }
    document.getElementById('db_tables').innerHTML = html;
  }


  static changeDB() {
    const elms = document.getElementsByName('db_name_radio');
    for (const elm of elms) {
      if (elm.checked) {
        DB.selectedDB = elm.value;
        DB.fetchDBTableNames();
        break;
      }
    }
  }

  static changeTable() {
    const elms = document.getElementsByName('table_name_radio');
    for (const elm of elms) {
      if (elm.checked) {
        DB.findTableDefine(elm.value);
        break;
      }
    }
  }

  static async fetchDBTableNames() {
    const data = await Api.fetchDBTableNames(DB.selectedDB);
    DB.makeTableNameTag(data.result);
  }

  static async findTableDefine(table) {
    const data = await Api.findTableDefine(DB.selectedDB, table);
    document.getElementById('db_result').innerHTML = data.result;
  }
}
