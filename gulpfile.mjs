import gulp from "gulp";

import imageMin from "./gulp_modules/gulpImageMin.js";
import processCSS from "./gulp_modules/gulpProcessCSS.js";
import processJS from "./gulp_modules/gulpProcessJS.js";
import processSW from "./gulp_modules/gulpProcessSW.js";
import { serveFiles, bs} from "./gulp_modules/gulpServe.js";
import gulpMakeVersion from "./gulp_modules/gulpMakeVersion.js";

import { deleteAsync } from "del";

const manifestSrc = 'public/assets/json/manifest.json';

const { series, parallel, watch, task } = gulp;



function watchFiles() {
      processCSS();
  processJS();
  watch("scss/**/*.scss", series(processCSS));
  watch("public/assets/sw.js", series(processSW));
  watch(['public/js/**/*.js', '!public/js/**/*.min.js'], series(processJS));
          watch([
        "App/**/*.php",         // Controllers, Models, etc.
        "App/Views/**/*.html",  // Twig templates
    ]).on('change', bs.reload);

}

function runClean() {
 return deleteAsync(["public/js/*.js","!public/modules/*.js","!public/js/app.js","public/css/*.css","public/sw.js","public/manifest.json","public/rev-manifest.json"]);
  
}




task("serve", parallel(serveFiles, watchFiles));

task("imageMin", imageMin);


task("build", series(runClean, parallel(processCSS, processJS, processSW), gulpMakeVersion));


task("default", series(runClean, parallel(processCSS, processJS, processSW), gulpMakeVersion));