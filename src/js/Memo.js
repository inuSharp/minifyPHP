class Memo {
  static indexGroup = [];

  static clear() {
    document.getElementById('memo').innerHTML = '';
  }

  static clearCheck() {
    Memo.fetchIndexes();
    const indexes = document.getElementsByName('checkIndex');
    for (const index of indexes) {
      index.checked = false;
    }
    Memo.clear();
  }

  static getGroupIndexName() {
    const indexes = document.getElementsByName('checkGroupIndex');
    let checkedGroupName = '';
    for (const index of indexes) {
      if (index.checked) {
        checkedGroupName = index.value;
        break;
      }
    }
    return checkedGroupName;
  }

  static changeGroupIndex() {
    Memo.clear();
    let html = '';
    let word = '';
    const checkedGroupName = Memo.getGroupIndexName();

    for (const group of Memo.indexGroup) {
      word = group.group_name;
      if (checkedGroupName === word) {
        const groupChildren = group.children;
        for (const child of groupChildren) {
            html += '<label><input name="checkIndex" type="checkbox" value="' + child + '" onchange="Memo.changeIndex()"> ' + child + '</label><br>';
        }
        break;
      }
    }
    document.getElementById('memo_index_children').innerHTML = html;
  }

  static async changeIndex() {
    const searchWords = [];
    const indexes = document.getElementsByName('checkIndex');
    for (const index of indexes) {
      if (index.checked) {
        searchWords.push(index.value);
      }
    }

    const checkedGroupName = Memo.getGroupIndexName();
    const w = searchWords.join(' ');
    Memo.searchMemo(checkedGroupName + ' ' + w);
  }

  static async fetchIndexes() {
    document.getElementById('memo_index_group').innerHTML = '';
    document.getElementById('memo_index_children').innerHTML = '';
    const data = await Api.fetchMemoIndex();
    Memo.indexGroup = data.result;
    Memo.makeGroupTag();
  }

  static makeGroupTag() {
    let html = '';
    let word = '';
    for (const group of Memo.indexGroup) {
      word = group.group_name;
      html += '<label><input name="checkGroupIndex" type="radio" value="' + word + '" onchange="Memo.changeGroupIndex()"> ' + word + '</label><br>';
    }
    document.getElementById('memo_index_group').innerHTML = html;
  }

  static async searchMemo(w) {
    Memo.clear();
    const data = await Api.searchMemo(w);
    document.getElementById('memo').innerHTML = data.result;

    window.setTimeout(() => {
      const pres = document.getElementsByTagName("pre");
      for (let i = 0; i < pres.length; i++) {
        pres[i].onclick = null;
      }
      for (let i = 0; i < pres.length; i++) {
        pres[i].onclick = () => { 
          Util.copyToClipBoad(pres[i].innerText.trim(), function () {
            document.getElementById('message').innerHTML = 'copied!';
            window.setTimeout(() => {
              document.getElementById('message').innerHTML = '';
            }, 2000);
          })
        }
      }
    }, 500);
  }
}
