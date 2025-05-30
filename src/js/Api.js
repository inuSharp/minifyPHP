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
}
