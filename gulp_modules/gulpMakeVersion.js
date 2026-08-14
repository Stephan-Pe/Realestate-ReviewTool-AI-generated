import gulp from "gulp";
import rev from "gulp-rev";
import revDel from "gulp-rev-delete-original";

const { dest } = gulp;

function gulpMakeVersion() {
  const isProd = process.env.NODE_ENV === 'prod';
  if (!isProd) {
    // Skip versioning in non-production builds
    return Promise.resolve();
  }
  return gulp
    .src(["public/css/*.css", "public/js/main.min.js" ], { base: "public" })
    .pipe(dest("public/"))
    .pipe(rev())
    .pipe(revDel())
    .pipe(dest("public/"))
    .pipe(rev.manifest())
    .pipe(dest("public/"));
}

export default gulpMakeVersion;
