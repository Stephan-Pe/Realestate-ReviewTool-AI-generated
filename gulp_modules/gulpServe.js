import browserSync from "browser-sync";
export const bs = browserSync.create();

// initialize BrowserSync proxy for XAMPP
export function serveFiles(done) {
  bs.init({
    proxy: "https://reviewtool.test", // XAMPP virtual host
    browser: ["chrome", "firefox"], // specify the browser to open
    listen: "reviewtool.test",
    open: "external",
    https: {
      key: "C:/xampp/apache/crt/reviewtool.test/server.key",
      cert: "C:/xampp/apache/crt/reviewtool.test/server.crt",
    },
    notify: false,
  });
  done();
}
