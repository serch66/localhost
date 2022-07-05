if(!window.QSoft)
  QSoft = {};

if(!QSoft.Personal)
  QSoft.Personal = {};

QSoft.Personal.Sync = function(options)
{
  this.run = false;
  this.btnStart = $('#sync_start');
  this.btnCancel = $('#sync_cancel');
  this.btnContinue = $('#sync_continue');
  this.notifyBlock = $('#notify');
  this.syncType = $('select[name="sync_type"]');
  this.syncFrom = $('input[name="sync_from"]');
  this.stepMaxTime = $('input[name="step_max_time"]');
  this.stepDelay = $('input[name="step_delay_time"]');

  this.init();
};

QSoft.Personal.Sync.prototype = {
  init: function() {
    let self = this;
    this.btnStart.on('click', function (event) {
      event.preventDefault();
      self.setStatus('start');
    });

    this.btnContinue.on('click', function (event) {
      event.preventDefault();
      self.setStatus('run');
    });

    this.btnCancel.on('click', function (event) {
      event.preventDefault();
      self.setStatus('cancel');
    });

    const checkType = function () {
      let val = $(':selected', self.syncType).val();
      if (val === 'full') {
        self.syncFrom.prop('disabled', true);
      } else {
        self.syncFrom.prop('disabled', false);
      }
    }

    this.syncType.on('change', function () {
      checkType();
    });
    checkType();

    this.syncFrom.on('click', function () {
      BX.calendar({node: this, field: this, bTime: true});
    })
  },

  setStatus: function(status) {
    switch (status) {
      case 'begin':
        this.run = false;
        this.btnStart.show();
        this.btnContinue.hide();
        this.btnCancel.hide();
        this.hideProgressBar();
        break;
      case 'start':
        this.btnStart.hide();
        this.btnContinue.hide();
        this.btnCancel.show();
        this.showProgressBar();
        this.hideNotify();
        this.actionStart();
        this.run = true;
        break;
      case 'run':
        this.run = true;
        this.btnStart.hide();
        this.btnContinue.hide();
        this.btnCancel.show();
        this.showProgressBar();
        this.actionRun()
        break;
      case 'cancel':
        this.run = false;
        this.btnStart.show();
        this.btnContinue.show();
        this.btnCancel.hide();
        this.actionCancel();
        break;
    }
  },

  actionStart: function() {
    if (this.run)
      return;

    let self = this;
    let dateFrom = this.syncFrom.prop('disabled') ? '' : this.syncFrom.val();
    let maxTime = parseInt(this.stepMaxTime.val());

    BX.ajax.runAction(
      'qsoft:personal.api.Sync.start',
      {
        data: {from: dateFrom, maxTime: maxTime},
        onrequeststart: function (xhr) {
          self.xhr = xhr;
        }
      }
    ).then(function (response) {
      self.responseHandler(response);
    },  function (response){
      self.errorHandler(response);
    });
  },

  actionCancel: function() {
    if (this.xhr) {
      this.xhr.abort();
    }

    BX.ajax.runAction('qsoft:personal.api.Sync.stop')
  },

  actionRun: function() {
    if (!this.run) {
      return;
    }

    let self = this;

    BX.ajax.runAction(
      'qsoft:personal.api.Sync.run',
      {
        onrequeststart: function (xhr) {
          self.xhr = xhr;
        }
      }
    ).then(function(response) {
      self.responseHandler(response);
    }, function (response){
      self.errorHandler(response);
    });
  },

  isResponseSuccess: function(response) {
    return response.hasOwnProperty('status') && response.status === 'success' && response.hasOwnProperty('data')
      && response.data.hasOwnProperty('status') && response.data.status === 'success';
  },

  responseHandler: function(response){
    const self = this;

    if(this.isResponseSuccess(response)) {
      this.setProgressInfo(response.data);
      if(
        response.data.hasOwnProperty('isFinished') &&
        response.data.isFinished === true
      ) {
        let message = '';
        if(response.hasOwnProperty('data') && response.data.hasOwnProperty('message')) {
          message = response.data.message;
        }

        this.showNotify(message, false);
        this.setStatus('begin');

      } else {
        let delay = parseInt(this.stepDelay.val());
        if (!delay) {
          delay = 0.1;
        }
        setTimeout(function () {
          self.actionRun();
        }, delay * 1000);
      }
    } else {
      let message = '';
      if(response.hasOwnProperty('data') && response.data.hasOwnProperty('message')) {
        message = response.data.message;
      }
      else if(response.hasOwnProperty('errors')){
        if(Array.isArray(response.errors))
          message = response.errors.join('<br />');
        else
          message = response.errors;
      }

      this.showNotify(message);
      this.setStatus('cancel');
    }
  },

  errorHandler: function(response){
    let message = '';
    if(Array.isArray(response.errors))
      message = response.errors.map(function(item){return item.message}).join('<br />');
    else
      message = response.errors;

    this.showNotify(message);
    this.setStatus('cancel');
  },

  getProgressBar: function () {
    if(!this.progressBar){
      this.progressBar = new BX.UI.ProgressBar({
        size: BX.UI.ProgressBar.Size.LARGE,
        value: 0,
        statusType: BX.UI.ProgressBar.Status.PERCENT,
        color: BX.UI.ProgressBar.Color.SUCCESS,
        column: true
      });

      $('#progress').append($(this.progressBar.getContainer()));
    }

    return this.progressBar;
  },

  showProgressBar: function(){
    const progressbar = this.getProgressBar();
    $(progressbar.getContainer()).show();
    return progressbar;
  },

  hideProgressBar: function(){
    $(this.getProgressBar().getContainer()).hide();
  },

  setProgressInfo: function(data) {

    if(! data.percentage) {
      data.percentage = 0;
    }
    if(! data.message) {
      data.message = BX.message('QSOFT_LDAP_MODULE_SYNC_PROCCESS');
    }

    const progress = this.showProgressBar();

    progress.update(data.percentage);
    progress.setTextBefore(data.message);
  },

  showNotify: function(message, isError) {
    if(typeof isError === 'undefined')
      isError = true;

    if(!message) {
      return;
    }

    const alert = new BX.UI.Alert({
      color: isError ? BX.UI.Alert.Color.DANGER : BX.UI.Alert.Color.SUCCESS,
      icon: isError ? BX.UI.Alert.Icon.DANGER : BX.UI.Alert.Icon.INFO,
      text: message,
    });

    this.notifyBlock.show();
    this.notifyBlock.append($(alert.getContainer()))
  },

  hideNotify: function() {
    this.notifyBlock.hide().empty();
  },
};
