class Api {
  static async get(url) {
    const response = await fetch(WEB_ROOT + url);

    if (!response.ok) {
      const data = await response.json();
      const error = {
        status: response.status,
        body: data
      };
      console.log(error);
      throw error;
    }

    const data = await response.json();
    return data;
  }

  static async fetchMemoIndex() {
    return await this.get('/api/memo/index');
  }

  static async searchMemo(w) {
    return await this.get('/api/memo/search?w=' + w);
  }

  static async fetchDBIndex() {
    return await this.get('/api/db/index');
  }

  static async fetchDBTableNames(d) {
    return await this.get('/api/db/table_names?d=' + d);
  }

  static async findTableDefine(d, t) {
    return await this.get('/api/db/table_define?d=' + d + '&t=' + t);
  }
}
