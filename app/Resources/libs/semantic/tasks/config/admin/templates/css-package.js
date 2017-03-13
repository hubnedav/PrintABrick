var
  where = 'client' // Adds files only to the client
;

Package.describe({
  name    : 'semantic:ui-css',
  summary : 'Semantic UI - CSS Release of Semantic UI',
  version : '{version}',
  git     : 'git://github.com/Semantic-Org/Semantic-UI-CSS.git',
});

Package.onUse(function(api) {

  api.versionsFrom('1.0');

  api.use('jquery', 'client');

  api.addFiles([
    // icons
    'themes/default/libs/fonts/icons.eot',
    'themes/default/libs/fonts/icons.svg',
    'themes/default/libs/fonts/icons.ttf',
    'themes/default/libs/fonts/icons.woff',
    'themes/default/libs/fonts/icons.woff2',

    // flags
    'themes/default/libs/images/flags.png',

    // release
    'semantic.css',
    'semantic.js'
  ], 'client');

});
