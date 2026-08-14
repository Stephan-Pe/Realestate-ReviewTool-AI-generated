import gulp from "gulp";
import babel from "gulp-babel";
import webpack from "webpack-stream";

const { dest } = gulp;

function processSW() {
    return gulp
        .src("public/assets/sw.js")
        .pipe(
            babel({
                plugins: ["@babel/transform-runtime"],
                presets: ["@babel/preset-env"],
            })
        )
        .pipe(webpack({
            mode: "production",
            output: {
                filename: "sw.js",
            }

        }))
        .pipe(dest("public/"));

}

export default processSW;
