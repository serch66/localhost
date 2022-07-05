if(!window.QSoft)
    QSoft = {};

if(!QSoft.Committees)
    QSoft.Committees = {};

QSoft.Committees.Sync = function(options)
{
    this.run = false;
    this.stop = false;
    this.btnStart = $('#sync_start');
    this.btnCancel = $('#sync_cancel');
    this.isUploading = $('#is_uploading');
    this.pageSync = $('#page_progress');
    this.uploadDateStartBtn = $('input[name=upload_date_start]');
    this.uploadDateEndBtn = $('input[name=upload_date_end]');
    this.meta = [];

    this.notifyBlock = $('#notify');

    this.init();
};

QSoft.Committees.Sync.prototype = {
    init: function() {
        this.isUploading.prop('disabled', true);

        if (!this.isUploading.prop('checked')) {
            this.btnCancel.hide();
        } else {
            this.btnStart.hide();
        }

        let self = this;

        this.btnStart.on('click', function (event) {
            self.stop = false;
            event.preventDefault();
            self.actionStart(false);
            self.btnCancel.show();
            self.btnStart.hide();
        });

        this.btnCancel.on('click', function (event) {
            event.preventDefault();
            self.stop = true;
            self.btnCancel.hide();
            self.hideProgressBar();
            self.btnStart.show();
            self.actionStart();
        });
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
        this.notifyBlock.hide();
    },

    isResponseSuccess: function(response) {
        return response.hasOwnProperty('status') && response.status === 'success' && response.hasOwnProperty('data');
    },

    setProgressInfo: function() {
        const progress = this.showProgressBar();

        progress.update(this.getPercentage());
        progress.setTextBefore(this.getTextAboveBar());
    },

    getTextAboveBar: function() {
        return 'Выгружено событий: ' + this.getLoadedEventsCount() + '/' + this.meta.totalCount;
    },

    getLoadedEventsCount: function() {
        let count = this.meta.perPage * this.meta.currentPage;
        return (count < this.meta.totalCount) ? count : this.meta.totalCount;
    },

    getPercentage: function() {
        return (this.getLoadedEventsCount() * 100) / this.meta.totalCount;
    },

    toggleStartPageButtons: function (state) {

        if(state === undefined || state !== true && state !== false) {
            return;
        }

        this.btnCancel.prop('disabled', !state);
        this.btnStart.prop('disabled', state);
    },

    actionStart: function() {
        if (this.stop) {
            BX.ajax.runAction('qsoft:committees.api.Sync.interrupt').then(
                function(response) {
                }
            );

            this.hideProgressBar();

            return;
        }

        let self = this;

        this.run = true;
        this.hideNotify();
        this.notifyBlock.empty();

        BX.ajax.runAction('qsoft:committees.api.Sync.start')
            .then(
                function (response) {
                    let status = response.data.status;

                    if (status === 'success') {
                        self.responseHandler(response);
                    }

                    if (status === 'error') {
                        let message = response.data.message;

                        BX.ajax.runAction('qsoft:committees.api.Sync.errorFinish').then(
                            function(response) {
                            }
                        );

                        self.showNotify(message);
                        self.run = false;
                        self.btnCancel.hide();
                        self.btnStart.show();
                    }
                }
            );
    },

    isFinished: function() {
        return this.meta.currentPage === this.meta.pageCount;
    },

    responseHandler: function(response){
        let self = this;

        if(self.isResponseSuccess(response)) {
            this.meta = response.data.meta;

            if (!this.stop) {
                this.setProgressInfo();
            }

            if (this.isFinished()) {
                this.run = false;

                BX.ajax.runAction('qsoft:committees.api.Sync.successFinish').then(
                    function(response) {
                    }
                );

                this.hideProgressBar();

                let message = 'Выгрузка событий завершена.';

                self.btnCancel.hide();
                self.btnStart.show();
                this.showNotify(message, false);

            } else {
                setTimeout(function () {
                    self.actionStart();
                }, 400);
            }
        } else {
            this.showNotify(response.message);
            this.run = false;
        }
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
    }
};
