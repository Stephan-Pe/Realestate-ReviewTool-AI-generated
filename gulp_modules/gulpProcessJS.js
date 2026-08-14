import gulp from "gulp";
import babel from "gulp-babel";
import webpack from "webpack-stream";
import browserSync from "browser-sync";

const bs = browserSync.create();

const { dest } = gulp;

function processJS() {
  return gulp
    .src(["public/js/*.js", "!public/js/*.min.js"])
    .pipe(
      babel({
        plugins: ["@babel/transform-runtime"],
        presets: ["@babel/preset-env"],
      }),
    )
    .pipe(
      webpack({
        mode: "production",
        output: {
          filename: "[name].min.js",
        },
      }),
    )
    .pipe(dest("public/js/"))
    .pipe(bs.stream());
}

export default processJS;
