import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import app from 'flarum/forum/app';

export default class GuestModal extends Modal {
  className() {
    return 'GuestModal Modal--small';
  }

  title() {
    return [
      m('i', { className: 'fas fa-user-secret' }),
      ' ',
      app.translator.trans('huseyinfiliz-guest.forum.modal_title')
    ];
  }

  content() {
    return m('div', { className: 'Modal-body' }, [
      m('p', { className: 'text-center' }, 
        app.translator.trans('huseyinfiliz-guest.forum.welcome_message')
      ),
      m(Button, {
        className: 'Button Button--primary Button--block',
        onclick: () => this.hide()
      }, app.translator.trans('huseyinfiliz-guest.forum.modal_understood'))
    ]);
  }

  static isDismissible = true;
}