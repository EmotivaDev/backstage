import gulp from 'gulp';
import open from 'gulp-open';

// Task to open the browser
gulp.task('open-app', function(){
  gulp.src('pages/dashboards/analytics.html')
  .pipe(open());
});