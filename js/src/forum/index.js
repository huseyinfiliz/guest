import app from 'flarum/forum/app';
import LogInButtons from 'flarum/components/LogInButtons';
import { extend } from 'flarum/common/extend';
import GuestLoginButton from './components/GuestLoginButton';
import GuestModal from './modals/GuestModal';

app.initializers.add('huseyinfiliz-guest', () => {
  // Add guest login button
  extend(LogInButtons.prototype, 'items', function (items) {
    if (app.session && app.session.user) return;

    const usernamePrefix = app.forum.attribute('huseyinfiliz-guest.username') || 'Guest';
    
    items.add(
      'guest',
      m(
        GuestLoginButton,
        {
          className: 'Button GuestLogin',
          usernamePrefix
        },
        [
          m('i', { className: 'fas fa-user-secret' }),
          ' ',
          app.translator.trans('huseyinfiliz-guest.forum.login_button')
        ]
      ),
      -10
    );
  });

  // Check for guest modal after app is ready
  extend(app, 'mount', () => {
    // Make sure session exists
    if (!app.session || !app.session.user) return;
    
    const justLoggedIn = localStorage.getItem('guestJustLoggedIn');
    if (justLoggedIn !== 'true') return;
    
    const prefix = app.forum.attribute('huseyinfiliz-guest.username') || 'Guest';
    const username = app.session.user.username();
    const regex = new RegExp(`^${prefix}\\d{4}$`);
    
    if (regex.test(username)) {
      localStorage.removeItem('guestJustLoggedIn');
      
      setTimeout(() => {
        app.modal.show(GuestModal);
      }, 500);
    }
  });
});