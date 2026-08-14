import gulp from "gulp";
import gulpIf from "gulp-if";
import imagemin, { gifsicle, mozjpeg, optipng, svgo } from "gulp-imagemin";

const isProd = process.env.NODE_ENV === "prod";
const { dest } = gulp;
const imgSource = "public/images/**/*.*";
async function imageMin() {
    try {
        return gulp.src([imgSource])
            .pipe(gulpIf(isProd, imagemin([
                gifsicle({ interlaced: true }),
                mozjpeg({ quality: 65, progressive: true }),
                optipng({ optimizationLevel: 5 }),
                svgo({
                    plugins: [
                        {
                            name: 'removeViewBox',
                            active: true
                        },
                        {
                            name: 'cleanupIDs',
                            active: false
                        }
                    ]
                })
            ])))
            .pipe(dest("public/images/"));
    } catch (error) {
        console.log(error);
    }

}

export default imageMin;