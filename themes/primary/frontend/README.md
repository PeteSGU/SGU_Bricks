Framework
=========

If you cloned this repository directly, you did a bad thing.
Go to your working directory to run gulp. The working directory for each CMS is as follows:

- Static Hand-off: .
- BigTree: /site/
- WordPress: /wp-content/themes/{repo-name}/primary/
- Drupal: /themes/custom/{repo-name}/

Gulp Tasks
----------
- Default (no task): Runs gulp watch, builds to /{working-directory}/local/ where you can see your changes.
- axe: Runs the accessibility scan locally and creates the "/static-html/axe.json" file.
- cms: This task builds to the root level directories and allows you to test your changes in the CMS environment. It prevents you from committing the compiled files.
- favicon: This task runs the realFaviconGenerator process and creates your /favicons files. 