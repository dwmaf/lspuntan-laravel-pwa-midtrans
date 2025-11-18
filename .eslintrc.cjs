// File: .eslintrc.cjs
module.exports = {
  root: true,
  extends: [
    "plugin:vue/vue3-recommended" // Ini adalah "buku aturan" yang kita butuhkan
  ],
  rules: {
    // Ini adalah aturan yang Anda tanyakan!
    "vue/html-self-closing": ["error", {
      "html": {
        "normal": "any", // 'any' memperbolehkan <div /> atau <div></div>
        "component": "always"
      }
    }]
  }
};