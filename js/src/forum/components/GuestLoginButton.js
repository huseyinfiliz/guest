import Button from 'flarum/common/components/Button';
import app from 'flarum/forum/app';

export default class GuestLoginButton extends Button {
  oninit(vnode) {
    super.oninit(vnode);
    
    this.usernamePrefix = this.attrs.usernamePrefix || 'Guest';
  }

  oncreate(vnode) {
    super.oncreate(vnode);
    
    $(vnode.dom).on('click', (e) => {
      e.preventDefault();
      this.handleGuestLogin();
    });
  }

  handleGuestLogin() {
    this.loading = true;
    m.redraw();
    
    app.request({
      method: 'POST',
      url: '/api/guest',
      data: {}
    }).then(response => {
      app.session.user = response;
      localStorage.setItem('guestJustLoggedIn', 'true');
      window.location.reload();
    }).catch(error => {
      console.error('Guest login error:', error);
      this.loading = false;
      m.redraw();
    });
  }
}