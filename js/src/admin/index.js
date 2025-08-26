import app from 'flarum/admin/app';

app.initializers.add('huseyinfiliz-guest', () => {
  app.extensionData
    .for('huseyinfiliz-guest')
    .registerSetting({
      setting: 'huseyinfiliz-guest.username',
      label: app.translator.trans('huseyinfiliz-guest.admin.username_label'),
      help: app.translator.trans('huseyinfiliz-guest.admin.username_help'),
      type: 'text',
    })
    .registerSetting({
      setting: 'huseyinfiliz-guest.max_posts',
      label: app.translator.trans('huseyinfiliz-guest.admin.max_posts_label'), 
      help: app.translator.trans('huseyinfiliz-guest.admin.max_posts_help'),
      type: 'number',
    });
});