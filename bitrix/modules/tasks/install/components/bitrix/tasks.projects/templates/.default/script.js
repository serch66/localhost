this.BX = this.BX || {};
this.BX.Tasks = this.BX.Tasks || {};
(function (exports,pull_client,main_loader,main_popup,main_core_events,ui_tour,main_core) {
	'use strict';

	/**
	 * @memberOf BX.Grid
	 */

	var CellActionState = function CellActionState() {
	  babelHelpers.classCallCheck(this, CellActionState);
	};
	babelHelpers.defineProperty(CellActionState, "SHOW_BY_HOVER", 'main-grid-cell-content-action-by-hover');
	babelHelpers.defineProperty(CellActionState, "ACTIVE", 'main-grid-cell-content-action-active');
	var namespace = main_core.Reflection.namespace('BX.Grid');
	namespace.CellActionState = CellActionState;

	var ActionsController = /*#__PURE__*/function () {
	  function ActionsController() {
	    babelHelpers.classCallCheck(this, ActionsController);
	  }

	  babelHelpers.createClass(ActionsController, null, [{
	    key: "setOptions",
	    value: function setOptions(options) {
	      ActionsController.options = options;
	    }
	  }, {
	    key: "setActionsPanel",
	    value: function setActionsPanel(actionsPanel) {
	      ActionsController.actionsPanel = actionsPanel;
	    }
	  }, {
	    key: "doAction",
	    value: function doAction(action, groupId) {
	      return new Promise(function (resolve, reject) {
	        BX.ajax.runComponentAction('bitrix:tasks.projects', 'processAction', {
	          mode: 'class',
	          data: {
	            action: action,
	            ids: ActionsController.getActionIds(groupId) || []
	          },
	          signedParameters: ActionsController.options.signedParameters
	        }).then(function () {
	          return resolve();
	        }, function () {
	          return reject();
	        }).catch(function () {
	          return reject();
	        });
	        ActionsController.hideActionsPanel();
	        ActionsController.unselectRows();
	      });
	    }
	  }, {
	    key: "getActionIds",
	    value: function getActionIds(id) {
	      if (id !== undefined) {
	        return [id];
	      }

	      var selected = ActionsController.getSelectedRows();

	      if (selected.length === 0) {
	        return [];
	      }

	      return selected.map(function (row) {
	        return row.getDataset().id;
	      });
	    }
	  }, {
	    key: "getSelectedRows",
	    value: function getSelectedRows() {
	      return ActionsController.getGridInstance().getRows().getSelected();
	    }
	  }, {
	    key: "sendJoinRequest",
	    value: function sendJoinRequest(button) {
	      if (main_core.Dom.hasClass(button, 'ui-btn-clock')) {
	        return;
	      }

	      main_core.Dom.addClass(button, 'ui-btn-clock');
	      BX.ajax({
	        url: button.getAttribute('bx-request-url'),
	        method: 'POST',
	        dataType: 'json',
	        data: {
	          ajax_request: 'Y',
	          save: 'Y',
	          sessid: BX.bitrix_sessid()
	        },
	        onsuccess: function onsuccess() {
	          return main_core.Dom.removeClass(button, 'ui-btn-clock');
	        },
	        onfailure: function onfailure() {
	          return main_core.Dom.removeClass(button, 'ui-btn-clock');
	        }
	      });
	      ActionsController.hideActionsPanel();
	      ActionsController.unselectRows();
	    }
	  }, {
	    key: "sendAcceptRequest",
	    value: function sendAcceptRequest(userGroupRelationId, url) {
	      BX.ajax({
	        url: url,
	        method: 'POST',
	        dataType: 'json',
	        data: {
	          action: 'accept',
	          max_count: 1,
	          checked_0: 'Y',
	          type_0: 'INVITE_GROUP',
	          id_0: userGroupRelationId,
	          type: 'in',
	          ajax_request: 'Y',
	          sessid: BX.bitrix_sessid()
	        },
	        onsuccess: function onsuccess(result) {},
	        onfailure: function onfailure(result) {
	          return console.log(result);
	        }
	      });
	      ActionsController.hideActionsPanel();
	      ActionsController.unselectRows();
	    }
	  }, {
	    key: "sendDenyRequest",
	    value: function sendDenyRequest(userGroupRelationId, url) {
	      ActionsController.sendCancelRequest(userGroupRelationId, url);
	    }
	  }, {
	    key: "sendCancelRequest",
	    value: function sendCancelRequest(userGroupRelationId, url) {
	      BX.ajax({
	        url: url,
	        method: 'POST',
	        dataType: 'json',
	        data: {
	          action: 'reject',
	          max_count: 1,
	          checked_0: 'Y',
	          type_0: 'INVITE_GROUP',
	          id_0: userGroupRelationId,
	          type: 'out',
	          ajax_request: 'Y',
	          sessid: BX.bitrix_sessid()
	        },
	        onsuccess: function onsuccess(result) {},
	        onfailure: function onfailure(result) {
	          return console.log(result);
	        }
	      });
	      ActionsController.hideActionsPanel();
	      ActionsController.unselectRows();
	    }
	  }, {
	    key: "hideActionsPanel",
	    value: function hideActionsPanel() {
	      ActionsController.options.actionsPanel.hidePanel();
	    }
	  }, {
	    key: "unselectRows",
	    value: function unselectRows() {
	      ActionsController.getGridInstance().getRows().unselectAll();
	    }
	  }, {
	    key: "getGridInstance",
	    value: function getGridInstance() {
	      return BX.Main.gridManager.getById(ActionsController.options.gridId).instance;
	    }
	  }, {
	    key: "changePin",
	    value: function changePin(event) {
	      var _event$getData = event.getData(),
	          button = _event$getData.button,
	          row = _event$getData.row;

	      if (main_core.Dom.hasClass(button, CellActionState.ACTIVE)) {
	        ActionsController.doAction('unpin', row.getId()).then(function () {
	          main_core.Dom.removeClass(button, CellActionState.ACTIVE);
	          main_core.Dom.addClass(button, CellActionState.SHOW_BY_HOVER);
	        });
	      } else {
	        ActionsController.doAction('pin', row.getId()).then(function () {
	          main_core.Dom.addClass(button, CellActionState.ACTIVE);
	          main_core.Dom.removeClass(button, CellActionState.SHOW_BY_HOVER);
	        });
	      }
	    }
	  }, {
	    key: "onCounterClick",
	    value: function onCounterClick(event) {
	      var _event$getData2 = event.getData(),
	          row = _event$getData2.row;

	      BX.SidePanel.Instance.open(ActionsController.options.groupTaskPath.replace('#group_id#', row.getId()));
	    }
	  }]);
	  return ActionsController;
	}();

	var Filter = /*#__PURE__*/function () {
	  function Filter(options) {
	    babelHelpers.classCallCheck(this, Filter);
	    this.filter = BX.Main.filterManager.getById(options.filterId);
	    this.init();
	    this.bindEvents();
	  }

	  babelHelpers.createClass(Filter, [{
	    key: "init",
	    value: function init() {
	      this.updateFields();
	    }
	  }, {
	    key: "bindEvents",
	    value: function bindEvents() {
	      main_core_events.EventEmitter.subscribe('BX.Main.Filter:apply', this.onFilterApply.bind(this));
	      main_core_events.EventEmitter.subscribe('Tasks.Toolbar:onItem', this.onCounterClick.bind(this));
	    }
	  }, {
	    key: "onFilterApply",
	    value: function onFilterApply() {
	      this.updateFields();
	    }
	  }, {
	    key: "updateFields",
	    value: function updateFields() {
	      this.fields = this.filter.getFilterFieldsValues();
	    }
	  }, {
	    key: "onCounterClick",
	    value: function onCounterClick(event) {
	      var data = event.getData();

	      if (data.counter && data.counter.filter) {
	        this.toggleByField(babelHelpers.defineProperty({}, data.counter.filterField, data.counter.filterValue));
	      }
	    }
	  }, {
	    key: "isFilteredByField",
	    value: function isFilteredByField(field) {
	      if (!Object.keys(this.fields).includes(field)) {
	        return false;
	      }

	      if (main_core.Type.isArray(this.fields[field])) {
	        return this.fields[field].length > 0;
	      }

	      return this.fields[field] !== '';
	    }
	  }, {
	    key: "isFilteredByFieldValue",
	    value: function isFilteredByFieldValue(field, value) {
	      return this.isFilteredByField(field) && this.fields[field] === value;
	    }
	  }, {
	    key: "toggleByField",
	    value: function toggleByField(field) {
	      var _this = this;

	      var name = Object.keys(field)[0];
	      var value = field[name];

	      if (!this.isFilteredByFieldValue(name, value)) {
	        this.filter.getApi().extendFilter(babelHelpers.defineProperty({}, name, value));
	        return;
	      }

	      this.filter.getFilterFields().forEach(function (field) {
	        if (field.getAttribute('data-name') === name) {
	          _this.filter.getFields().deleteField(field);
	        }
	      });
	      this.filter.getSearch().apply();
	    }
	  }]);
	  return Filter;
	}();

	var _templateObject, _templateObject2, _templateObject3, _templateObject4;
	var MembersPopup = /*#__PURE__*/function () {
	  babelHelpers.createClass(MembersPopup, null, [{
	    key: "titles",
	    get: function get() {
	      return {
	        heads: main_core.Loc.getMessage('TASKS_PROJECTS_MEMBERS_POPUP_TITLE_HEADS'),
	        members: main_core.Loc.getMessage('TASKS_PROJECTS_MEMBERS_POPUP_TITLE_MEMBERS')
	      };
	    }
	  }]);

	  function MembersPopup(options) {
	    babelHelpers.classCallCheck(this, MembersPopup);
	    this.signedParameters = options.signedParameters;
	  }

	  babelHelpers.createClass(MembersPopup, [{
	    key: "show",
	    value: function show(groupId, type, bindNode) {
	      var _this = this;

	      if (this.isPopupShown) {
	        this.popup.destroy();
	      }

	      this.currentPage = 1;
	      this.innerContainer = '';
	      this.renderedUsers = [];
	      this.popup = main_popup.PopupWindowManager.create({
	        id: 'projects-members-popup-menu',
	        className: 'tasks-projects-members-popup',
	        bindElement: bindNode,
	        autoHide: true,
	        closeByEsc: true,
	        lightShadow: true,
	        bindOptions: {
	          position: 'bottom'
	        },
	        animationOptions: {
	          show: {
	            type: 'opacity-transform'
	          },
	          close: {
	            type: 'opacity'
	          }
	        },
	        events: {
	          onPopupDestroy: function onPopupDestroy() {
	            _this.loader = null;
	            _this.isPopupShown = false;
	          },
	          onPopupClose: function onPopupClose() {
	            _this.popup.destroy();
	          },
	          onAfterPopupShow: function onAfterPopupShow(popup) {
	            var popupContainer = _this.renderContainer(type);

	            var loaderNode = popupContainer.querySelector('.tasks-projects-members-popup-content');
	            _this.innerContainer = popupContainer.querySelector('.tasks-projects-members-popup-inner');
	            popup.contentContainer.appendChild(popupContainer);

	            _this.showLoader(loaderNode);

	            _this.showUsers(groupId, type);

	            _this.isPopupShown = true;
	          }
	        }
	      });
	      this.popupScroll(groupId, type);
	      this.popup.show();
	    }
	  }, {
	    key: "renderContainer",
	    value: function renderContainer(type) {
	      return main_core.Tag.render(_templateObject || (_templateObject = babelHelpers.taggedTemplateLiteral(["\n\t\t\t<div>\n\t\t\t\t<span class=\"tasks-projects-members-popup-name-title\">\n\t\t\t\t\t", "\n\t\t\t\t</span>\n\t\t\t\t<div class=\"tasks-projects-members-popup-container\">\n\t\t\t\t\t<div class=\"tasks-projects-members-popup-content\">\n\t\t\t\t\t\t<div class=\"tasks-projects-members-popup-content-box\">\n\t\t\t\t\t\t\t<div class=\"tasks-projects-members-popup-inner\"></div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t"])), MembersPopup.titles[type]);
	    }
	  }, {
	    key: "popupScroll",
	    value: function popupScroll(groupId, type) {
	      var _this2 = this;

	      if (!BX.type.isDomNode(this.innerContainer)) {
	        return;
	      }

	      main_core.Event.bind(this.innerContainer, 'scroll', function (event) {
	        var area = event.target;

	        if (area.scrollTop > (area.scrollHeight - area.offsetHeight) / 1.5) {
	          _this2.showUsers(groupId, type);

	          main_core.Event.unbindAll(_this2.innerContainer);
	        }
	      });
	    }
	  }, {
	    key: "showUsers",
	    value: function showUsers(groupId, type) {
	      var _this3 = this;

	      BX.ajax.runComponentAction('bitrix:tasks.projects', 'getPopupMembers', {
	        mode: 'class',
	        data: {
	          groupId: groupId,
	          type: type,
	          page: this.currentPage
	        },
	        signedParameters: this.signedParameters
	      }).then(function (response) {
	        if (response.data) {
	          _this3.currentPage++;

	          _this3.renderUsers(response.data);

	          _this3.popupScroll(groupId, type);
	        } else if (!_this3.innerContainer.hasChildNodes()) {
	          _this3.innerContainer.innerText = main_core.Loc.getMessage('TASKS_PROJECTS_MEMBERS_POPUP_EMPTY');
	        }

	        _this3.hideLoader();
	      }, function () {
	        return _this3.hideLoader();
	      });
	    }
	  }, {
	    key: "renderUsers",
	    value: function renderUsers(users) {
	      var _this4 = this;

	      Object.values(users).forEach(function (user) {
	        if (_this4.renderedUsers.indexOf(user.ID) >= 0) {
	          return;
	        }

	        _this4.renderedUsers.push(user.ID);

	        _this4.innerContainer.appendChild(main_core.Tag.render(_templateObject2 || (_templateObject2 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t\t<a class=\"tasks-projects-members-popup-item\" href=\"", "\" target=\"_blank\">\n\t\t\t\t\t\t<span class=\"tasks-projects-members-popup-avatar-new\">\n\t\t\t\t\t\t\t", "\n\t\t\t\t\t\t\t<span class=\"tasks-projects-members-popup-avatar-status-icon\"></span>\n\t\t\t\t\t\t</span>\n\t\t\t\t\t\t<span class=\"tasks-projects-members-popup-name\">", "</span>\n\t\t\t\t\t</a>\n\t\t\t\t"])), user['HREF'], _this4.getAvatar(user), user['FORMATTED_NAME']));
	      });
	    }
	  }, {
	    key: "getAvatar",
	    value: function getAvatar(user) {
	      if (main_core.Type.isStringFilled(user['PHOTO'])) {
	        return main_core.Tag.render(_templateObject3 || (_templateObject3 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t\t<div class=\"ui-icon ui-icon-common-user tasks-projects-members-popup-avatar-img\">\n\t\t\t\t\t<i style=\"background-image: url('", "')\"></i>\n\t\t\t\t</div>\n\t\t\t"])), user['PHOTO']);
	      }

	      return main_core.Tag.render(_templateObject4 || (_templateObject4 = babelHelpers.taggedTemplateLiteral(["\n\t\t\t<div class=\"ui-icon ui-icon-common-user tasks-projects-members-popup-avatar-img\"><i></i></div>\n\t\t"])));
	    }
	  }, {
	    key: "showLoader",
	    value: function showLoader(target) {
	      if (!this.loader) {
	        this.loader = new main_loader.Loader({
	          target: target,
	          size: 40
	        });
	      }

	      void this.loader.show();
	    }
	  }, {
	    key: "hideLoader",
	    value: function hideLoader() {
	      if (this.loader) {
	        void this.loader.hide();
	        this.loader = null;
	      }
	    }
	  }]);
	  return MembersPopup;
	}();

	var Grid = /*#__PURE__*/function () {
	  babelHelpers.createClass(Grid, null, [{
	    key: "classes",
	    get: function get() {
	      return {
	        highlighted: 'task-projects-item-highlighted',
	        pinned: 'tasks-projects-row-pinned'
	      };
	    }
	  }]);

	  function Grid(options) {
	    babelHelpers.classCallCheck(this, Grid);
	    this.pageSize = 10;
	    this.grid = BX.Main.gridManager.getInstanceById(options.gridId);
	    this.stub = options.gridStub;
	    this.sort = options.sort;
	    this.actionsPanel = options.actionsPanel;
	    this.eventsToEmit = options.eventsToEmit || {};
	    this.items = new Map();
	    this.fillItems(options.items);
	    this.bindEvents();
	    this.colorPinnedRows();
	  }

	  babelHelpers.createClass(Grid, [{
	    key: "bindEvents",
	    value: function bindEvents() {
	      main_core_events.EventEmitter.subscribe('BX.Main.grid:sort', this.onColumnSort.bind(this));
	      main_core_events.EventEmitter.subscribe('BX.Main.grid:paramsUpdated', this.onParamsUpdated.bind(this));
	    }
	  }, {
	    key: "onColumnSort",
	    value: function onColumnSort(event) {
	      var data = event.getData();
	      var grid = data[1];
	      var column = data[0];

	      if (grid === this.grid) {
	        this.sort = {};
	        this.sort[column.sort_by] = column.sort_order;
	      }
	    }
	  }, {
	    key: "onParamsUpdated",
	    value: function onParamsUpdated() {
	      var newItems = this.getRows().map(function (row) {
	        return row.getId();
	      }).filter(function (id) {
	        return id !== 'template_0';
	      });
	      var flipped = newItems.reduce(function (obj, value) {
	        return babelHelpers.objectSpread({}, obj, babelHelpers.defineProperty({}, value, null));
	      }, {});
	      this.clearItems();
	      this.fillItems(flipped);
	      this.colorPinnedRows();
	    }
	  }, {
	    key: "addRow",
	    value: function addRow(id, data, params) {
	      var options = {
	        id: id,
	        columns: data.content,
	        actions: data.actions,
	        cellActions: data.cellActions,
	        counters: data.counters
	      };
	      var moveParams = params.moveParams || {};

	      if (moveParams.rowBefore) {
	        options.insertAfter = moveParams.rowBefore;
	      } else if (moveParams.rowAfter) {
	        options.insertBefore = moveParams.rowAfter;
	      } else {
	        options.append = true;
	      }

	      if (this.items.size > this.getCurrentPage() * this.pageSize) {
	        var lastRowId = this.getLastRowId();
	        this.removeItem(lastRowId);
	        main_core.Dom.remove(this.getRowNodeById(lastRowId));
	        this.showMoreButton();
	      }

	      this.hideStub();
	      this.getRealtime().addRow(options);
	      this.colorPinnedRows();

	      if (this.eventsToEmit.onProjectGridRowAdd) {
	        main_core_events.EventEmitter.emit('Tasks.Projects.Grid:RowAdd', {
	          id: id
	        });
	      }
	    }
	  }, {
	    key: "updateRow",
	    value: function updateRow(id, data, params) {
	      var _this = this;

	      var row = this.getRowById(id);
	      row.setCellsContent(data.content);
	      row.setActions(data.actions);
	      row.setCellActions(data.cellActions);
	      row.setCounters(data.counters);
	      this.resetRows();
	      this.moveRow(id, params.moveParams || {});
	      this.highlightRow(id, params.highlightParams || {}).then(function () {
	        return _this.colorPinnedRows();
	      }, function () {});
	      this.grid.bindOnRowEvents();
	    }
	  }, {
	    key: "removeRow",
	    value: function removeRow(id) {
	      if (!this.isRowExist(id)) {
	        return;
	      }

	      this.grid.removeRow(id);
	    }
	  }, {
	    key: "moveRow",
	    value: function moveRow(rowId, params) {
	      if (params.skip) {
	        return;
	      }

	      var rowBefore = params.rowBefore || 0;
	      var rowAfter = params.rowAfter || 0;

	      if (rowBefore) {
	        this.grid.getRows().insertAfter(rowId, rowBefore);
	      } else if (rowAfter) {
	        this.grid.getRows().insertBefore(rowId, rowAfter);
	      }
	    }
	  }, {
	    key: "highlightRow",
	    value: function highlightRow(rowId, params) {
	      var _this2 = this;

	      params = params || {};
	      return new Promise(function (resolve, reject) {
	        if (!_this2.isRowExist(rowId)) {
	          reject();
	          return;
	        }

	        if (params.skip) {
	          resolve();
	          return;
	        }

	        var node = _this2.getRowNodeById(rowId);

	        var isPinned = main_core.Dom.hasClass(node, Grid.classes.pinned);

	        if (isPinned) {
	          main_core.Dom.removeClass(node, Grid.classes.pinned);
	        }

	        main_core.Dom.addClass(node, Grid.classes.highlighted);
	        setTimeout(function () {
	          main_core.Dom.removeClass(node, Grid.classes.highlighted);

	          if (isPinned) {
	            main_core.Dom.addClass(node, Grid.classes.pinned);
	          }

	          resolve();
	        }, 900);
	      });
	    }
	  }, {
	    key: "colorPinnedRows",
	    value: function colorPinnedRows() {
	      var _this3 = this;

	      this.getRows().forEach(function (row) {
	        var node = row.getNode();
	        _this3.getIsPinned(row.getId()) ? main_core.Dom.addClass(node, Grid.classes.pinned) : main_core.Dom.removeClass(node, Grid.classes.pinned);
	      });
	    }
	  }, {
	    key: "resetRows",
	    value: function resetRows() {
	      this.grid.getRows().reset();
	    }
	  }, {
	    key: "getRows",
	    value: function getRows() {
	      return this.grid.getRows().getBodyChild();
	    }
	  }, {
	    key: "getFirstRowId",
	    value: function getFirstRowId() {
	      var firstRow = this.grid.getRows().getBodyFirstChild();
	      return firstRow ? this.getRowProperty(firstRow, 'id') : 0;
	    }
	  }, {
	    key: "getLastRowId",
	    value: function getLastRowId() {
	      var lastRow = this.grid.getRows().getBodyLastChild();
	      return lastRow ? this.getRowProperty(lastRow, 'id') : 0;
	    }
	  }, {
	    key: "getLastPinnedRowId",
	    value: function getLastPinnedRowId() {
	      var _this4 = this;

	      var pinnedRows = Object.values(this.getRows()).filter(function (row) {
	        return _this4.getIsPinned(row.getId());
	      });
	      var keys = Object.keys(pinnedRows);

	      if (keys.length > 0) {
	        return pinnedRows[keys[keys.length - 1]].getId();
	      }

	      return 0;
	    }
	  }, {
	    key: "getIsPinned",
	    value: function getIsPinned(rowId) {
	      return this.isRowExist(rowId) && main_core.Type.isDomNode(this.getRowNodeById(rowId).querySelector('.main-grid-cell-content-action-pin.main-grid-cell-content-action-active'));
	    }
	  }, {
	    key: "getRowProperty",
	    value: function getRowProperty(row, propertyName) {
	      return BX.data(row.getNode(), propertyName);
	    }
	  }, {
	    key: "getRowById",
	    value: function getRowById(id) {
	      return this.grid.getRows().getById(id);
	    }
	  }, {
	    key: "getRowNodeById",
	    value: function getRowNodeById(id) {
	      return this.getRowById(id).getNode();
	    }
	  }, {
	    key: "isRowExist",
	    value: function isRowExist(id) {
	      return this.getRowById(id) !== null;
	    }
	  }, {
	    key: "getCurrentPage",
	    value: function getCurrentPage() {
	      var currentPage = this.grid.getContainer().querySelector('.modern-page-current');
	      return currentPage ? currentPage.innerText : 1;
	    }
	  }, {
	    key: "isActivityRealtimeMode",
	    value: function isActivityRealtimeMode() {
	      return this.sort.ACTIVITY_DATE && this.sort.ACTIVITY_DATE === 'desc';
	    }
	  }, {
	    key: "getItems",
	    value: function getItems() {
	      return Array.from(this.items.keys());
	    }
	  }, {
	    key: "hasItem",
	    value: function hasItem(id) {
	      return this.items.has(parseInt(id, 10));
	    }
	  }, {
	    key: "addItem",
	    value: function addItem(id) {
	      this.items.set(parseInt(id, 10));
	    }
	  }, {
	    key: "removeItem",
	    value: function removeItem(id) {
	      this.items.delete(parseInt(id, 10));
	    }
	  }, {
	    key: "fillItems",
	    value: function fillItems(items) {
	      var _this5 = this;

	      Object.keys(items).forEach(function (id) {
	        return _this5.addItem(id);
	      });
	    }
	  }, {
	    key: "clearItems",
	    value: function clearItems() {
	      this.items.clear();
	    }
	  }, {
	    key: "getRealtime",
	    value: function getRealtime() {
	      return this.grid.getRealtime();
	    }
	  }, {
	    key: "showStub",
	    value: function showStub() {
	      if (this.stub) {
	        this.getRealtime().showStub({
	          content: this.stub
	        });
	      }
	    }
	  }, {
	    key: "hideStub",
	    value: function hideStub() {
	      this.grid.hideEmptyStub();
	    }
	  }, {
	    key: "showMoreButton",
	    value: function showMoreButton() {
	      this.grid.getMoreButton().getNode().style.display = 'inline-block';
	    }
	  }, {
	    key: "hideMoreButton",
	    value: function hideMoreButton() {
	      this.grid.getMoreButton().getNode().style.display = 'none';
	    }
	  }]);
	  return Grid;
	}();

	var PullController = /*#__PURE__*/function () {
	  babelHelpers.createClass(PullController, null, [{
	    key: "events",
	    get: function get() {
	      return {
	        add: 'add',
	        update: 'update',
	        remove: 'remove',
	        userAdd: 'userAdd',
	        userUpdate: 'userUpdate',
	        userRemove: 'userRemove',
	        pinChanged: 'pinChanged'
	      };
	    }
	  }, {
	    key: "counterEvents",
	    get: function get() {
	      return ['onAfterTaskAdd', 'onAfterTaskDelete', 'onAfterTaskRestore', 'onAfterTaskView', 'onAfterTaskMute', 'onAfterCommentAdd', 'onAfterCommentDelete', 'onProjectPermUpdate'];
	    }
	  }, {
	    key: "movingProjectEvents",
	    get: function get() {
	      return ['onAfterTaskAdd', 'onAfterCommentAdd'];
	    }
	  }]);

	  function PullController(options) {
	    babelHelpers.classCallCheck(this, PullController);
	    this.signedParameters = options.signedParameters;
	    this.grid = new Grid(options);
	    this.timer = null;
	    this.counterData = new Map();
	    this.userOptions = options.userOptions;
	  }

	  babelHelpers.createClass(PullController, [{
	    key: "getModuleId",
	    value: function getModuleId() {
	      return 'tasks';
	    }
	  }, {
	    key: "getMap",
	    value: function getMap() {
	      return {
	        project_add: this.onProjectAdd.bind(this),
	        project_update: this.onProjectUpdate.bind(this),
	        project_remove: this.onProjectRemove.bind(this),
	        project_user_add: this.onProjectUserAdd.bind(this),
	        project_user_update: this.onProjectUserUpdate.bind(this),
	        project_user_remove: this.onProjectUserRemove.bind(this),
	        project_user_option_changed: this.onProjectUserOptionChanged.bind(this),
	        project_counter: this.onProjectCounter.bind(this),
	        project_read_all: this.onProjectCommentsReadAll.bind(this),
	        comment_read_all: this.onProjectCommentsReadAll.bind(this)
	      };
	    }
	  }, {
	    key: "onProjectAdd",
	    value: function onProjectAdd(data) {
	      var _this = this;

	      var params = {
	        event: PullController.events.add,
	        moveParams: {
	          rowBefore: this.grid.getLastPinnedRowId(),
	          rowAfter: this.grid.getFirstRowId()
	        }
	      };
	      this.checkExistence(data.ID).then(function (response) {
	        return _this.onCheckExistenceSuccess(response, data.ID, params);
	      }, function (response) {
	        return console.error(response);
	      });
	    }
	  }, {
	    key: "onProjectUpdate",
	    value: function onProjectUpdate(data) {
	      var _this2 = this;

	      var params = {
	        event: PullController.events.update
	      };
	      this.checkExistence(data.ID).then(function (response) {
	        return _this2.onCheckExistenceSuccess(response, data.ID, params);
	      }, function (response) {
	        return console.error(response);
	      });
	    }
	  }, {
	    key: "onProjectRemove",
	    value: function onProjectRemove(data) {
	      this.removeRow(data.ID);
	    }
	  }, {
	    key: "onProjectUserAdd",
	    value: function onProjectUserAdd(data) {
	      var _this3 = this;

	      var params = {
	        event: PullController.events.userAdd
	      };
	      this.checkExistence(data.GROUP_ID).then(function (response) {
	        return _this3.onCheckExistenceSuccess(response, data.GROUP_ID, params);
	      }, function (response) {
	        return console.error(response);
	      });
	    }
	  }, {
	    key: "onProjectUserUpdate",
	    value: function onProjectUserUpdate(data) {
	      var _this4 = this;

	      var params = {
	        event: PullController.events.userUpdate
	      };
	      this.checkExistence(data.GROUP_ID).then(function (response) {
	        return _this4.onCheckExistenceSuccess(response, data.GROUP_ID, params);
	      }, function (response) {
	        return console.error(response);
	      });
	    }
	  }, {
	    key: "onProjectUserRemove",
	    value: function onProjectUserRemove(data) {
	      var _this5 = this;

	      var params = {
	        event: PullController.events.userRemove
	      };
	      this.checkExistence(data.GROUP_ID).then(function (response) {
	        return _this5.onCheckExistenceSuccess(response, data.GROUP_ID, params);
	      }, function (response) {
	        return console.error(response);
	      });
	    }
	  }, {
	    key: "onProjectUserOptionChanged",
	    value: function onProjectUserOptionChanged(data) {
	      switch (data.OPTION) {
	        case this.userOptions.pinned:
	          this.onProjectPinChanged(data);
	          break;

	        default:
	          break;
	      }
	    }
	  }, {
	    key: "onProjectPinChanged",
	    value: function onProjectPinChanged(data) {
	      var params = {
	        event: PullController.events.pinChanged
	      };
	      this.moveToDirectPlace(data.PROJECT_ID, null, params);
	    }
	  }, {
	    key: "onProjectCounter",
	    value: function onProjectCounter(data) {
	      var _this6 = this;

	      var groupId = data.GROUP_ID;
	      var event = data.EVENT;

	      if (!PullController.counterEvents.includes(event)) {
	        return;
	      }

	      if (!this.timer) {
	        this.timer = setTimeout(function () {
	          _this6.freeCounterQueue();
	        }, 1000);
	      }

	      if (PullController.movingProjectEvents.includes(event) || !this.counterData.has(groupId)) {
	        this.counterData.set(groupId, event);
	      }
	    }
	  }, {
	    key: "freeCounterQueue",
	    value: function freeCounterQueue() {
	      var _this7 = this;

	      this.counterData.forEach(function (event, groupId) {
	        var params = {
	          event: event,
	          highlightParams: {
	            skip: true
	          }
	        };

	        if (PullController.movingProjectEvents.includes(event)) {
	          params.moveParams = {
	            rowBefore: _this7.grid.getIsPinned(groupId) ? 0 : _this7.grid.getLastPinnedRowId(),
	            rowAfter: _this7.grid.getFirstRowId()
	          };
	          params.highlightParams = {
	            skip: false
	          };
	        }

	        _this7.checkExistence(groupId).then(function (response) {
	          return _this7.onCheckExistenceSuccess(response, groupId, params);
	        }, function (response) {
	          return console.error(response);
	        });
	      });
	      this.counterData.clear();
	      this.timer = null;
	    }
	  }, {
	    key: "onProjectCommentsReadAll",
	    value: function onProjectCommentsReadAll(data) {
	      var groupId = data.GROUP_ID;

	      if (groupId) {
	        if (this.grid.isRowExist(groupId)) {
	          this.updateCounter([groupId]);
	        }
	      } else {
	        this.updateCounter(this.grid.getItems());
	      }
	    }
	  }, {
	    key: "checkExistence",
	    value: function checkExistence(groupId) {
	      var _this8 = this;

	      return new Promise(function (resolve, reject) {
	        BX.ajax.runComponentAction('bitrix:tasks.projects', 'checkExistence', {
	          mode: 'class',
	          data: {
	            groupIds: [groupId]
	          },
	          signedParameters: _this8.signedParameters
	        }).then(function (response) {
	          return resolve(response);
	        }, function (response) {
	          return reject(response);
	        });
	      });
	    }
	  }, {
	    key: "onCheckExistenceSuccess",
	    value: function onCheckExistenceSuccess(response, groupId, params) {
	      if (response.data[groupId] === false) {
	        this.removeRow(groupId);
	        return;
	      }

	      var group = response.data[groupId];

	      if (this.grid.isRowExist(groupId)) {
	        this.grid.isActivityRealtimeMode() ? this.updateRow(groupId, group, params) : this.moveToDirectPlace(groupId, group, params);
	      } else if (this.grid.isActivityRealtimeMode() && (PullController.movingProjectEvents.includes(params.event) || params.event === PullController.events.add)) {
	        this.updateItem(groupId, group, params);
	      } else {
	        this.moveToDirectPlace(groupId, group, params);
	      }
	    }
	  }, {
	    key: "updateItem",
	    value: function updateItem(rowId, rowData, params) {
	      if (!this.grid.hasItem(rowId)) {
	        this.grid.addItem(rowId);
	        this.addRow(rowId, rowData, params);
	      } else {
	        this.updateRow(rowId, rowData, params);
	      }
	    }
	  }, {
	    key: "addRow",
	    value: function addRow(rowId, rowData, params) {
	      var _this9 = this;

	      if (this.grid.isRowExist(rowId)) {
	        return;
	      }

	      BX.ajax.runComponentAction('bitrix:tasks.projects', 'prepareGridRows', {
	        mode: 'class',
	        data: {
	          groupIds: [rowId],
	          data: rowData ? babelHelpers.defineProperty({}, rowId, rowData) : null
	        },
	        signedParameters: this.signedParameters
	      }).then(function (response) {
	        return _this9.grid.addRow(rowId, response.data[rowId], params);
	      });
	    }
	  }, {
	    key: "updateRow",
	    value: function updateRow(rowId, rowData, params) {
	      var _this10 = this;

	      if (!this.grid.isRowExist(rowId)) {
	        return;
	      }

	      BX.ajax.runComponentAction('bitrix:tasks.projects', 'prepareGridRows', {
	        mode: 'class',
	        data: {
	          groupIds: [rowId],
	          data: rowData ? babelHelpers.defineProperty({}, rowId, rowData) : null
	        },
	        signedParameters: this.signedParameters
	      }).then(function (response) {
	        return _this10.grid.updateRow(rowId, response.data[rowId], params);
	      });
	    }
	  }, {
	    key: "removeRow",
	    value: function removeRow(rowId) {
	      this.grid.removeItem(rowId);
	      this.grid.removeRow(rowId);
	    }
	  }, {
	    key: "moveToDirectPlace",
	    value: function moveToDirectPlace(groupId, data, params) {
	      var _this11 = this;

	      params = params || {};
	      BX.ajax.runComponentAction('bitrix:tasks.projects', 'findProjectPlace', {
	        mode: 'class',
	        data: {
	          groupId: groupId,
	          currentPage: this.grid.getCurrentPage()
	        },
	        signedParameters: this.signedParameters
	      }).then(function (response) {
	        var _response$data = response.data,
	            projectBefore = _response$data.projectBefore,
	            projectAfter = _response$data.projectAfter;

	        if (projectBefore === false && projectAfter === false) {
	          _this11.removeRow(groupId);
	        } else {
	          if (projectBefore && _this11.grid.isRowExist(projectBefore) || projectAfter && _this11.grid.isRowExist(projectAfter)) {
	            params.moveParams = {
	              rowBefore: projectBefore,
	              rowAfter: projectAfter
	            };
	          } else {
	            params.moveParams = {
	              skip: true
	            };
	          }

	          _this11.updateItem(groupId, data, params);
	        }
	      });
	    }
	  }, {
	    key: "updateCounter",
	    value: function updateCounter(rowIds) {
	      var _this12 = this;

	      BX.ajax.runComponentAction('bitrix:tasks.projects', 'prepareGridRows', {
	        mode: 'class',
	        data: {
	          groupIds: rowIds,
	          data: null
	        },
	        signedParameters: this.signedParameters
	      }).then(function (response) {
	        var projects = response.data;

	        if (projects) {
	          Object.keys(projects).forEach(function (projectId) {
	            if (_this12.grid.isRowExist(projectId)) {
	              _this12.grid.getRowById(projectId).setCounters(projects[projectId].counters);
	            }
	          });
	        }
	      });
	    }
	  }]);
	  return PullController;
	}();

	var FirstProjectCreationTourGuide = /*#__PURE__*/function () {
	  function FirstProjectCreationTourGuide(options) {
	    babelHelpers.classCallCheck(this, FirstProjectCreationTourGuide);
	    options.eventsToEmit = {
	      onProjectGridRowAdd: true
	    };
	    this.grid = new Grid(options);
	    this.signedParameters = options.signedParameters;
	    this.popupData = options.tours.firstProjectCreation.popupData;
	    this.projectAddButton = BX('projectAddButton');
	    this.guide = new ui_tour.Guide({
	      steps: [{
	        target: this.projectAddButton,
	        title: this.popupData[0].title,
	        text: this.popupData[0].text,
	        article: this.popupData[0].article
	      }],
	      onEvents: true
	    });
	    this.bindEvents();
	  }

	  babelHelpers.createClass(FirstProjectCreationTourGuide, [{
	    key: "bindEvents",
	    value: function bindEvents() {
	      main_core_events.EventEmitter.subscribe('UI.Tour.Guide:onFinish', this.onGuideFinish.bind(this));
	      main_core_events.EventEmitter.subscribe('SidePanel.Slider:onMessage', this.onProjectSliderMessage.bind(this));
	    }
	  }, {
	    key: "onGuideFinish",
	    value: function onGuideFinish(event) {
	      var _event$getData = event.getData(),
	          guide = _event$getData.guide;

	      if (guide === this.guide) {
	        this.projectAddButton.href = main_core.Uri.removeParam(this.projectAddButton.href, ['PROJECT_OPTIONS']);
	      }
	    }
	  }, {
	    key: "onProjectSliderMessage",
	    value: function onProjectSliderMessage(event) {
	      var _event$getData2 = event.getData(),
	          _event$getData3 = babelHelpers.slicedToArray(_event$getData2, 1),
	          sliderEvent = _event$getData3[0];

	      if (sliderEvent.getEventId() !== 'sonetGroupEvent') {
	        return;
	      }

	      var sliderEventData = sliderEvent.getData();

	      if (sliderEventData.code !== 'afterCreate' || sliderEventData.data.projectOptions.tourId !== this.guide.getId()) {
	        return;
	      }

	      var projectId = sliderEventData.data.group.ID;

	      if (this.grid.isRowExist(projectId)) {
	        this.showFinalStep(projectId);
	      } else {
	        main_core_events.EventEmitter.subscribe('Tasks.Projects.Grid:RowAdd', this.onProjectRowAdded.bind(this, projectId));
	      }
	    }
	  }, {
	    key: "onProjectRowAdded",
	    value: function onProjectRowAdded(projectId, event) {
	      var _event$getData4 = event.getData(),
	          id = _event$getData4.id;

	      if (Number(id) === Number(projectId)) {
	        this.showFinalStep(projectId);
	      }
	    }
	  }, {
	    key: "showFinalStep",
	    value: function showFinalStep(projectId) {
	      var _this = this;

	      var target = this.grid.getRowNodeById(projectId).querySelector('.tasks-projects-text');
	      this.guide.steps.push(new ui_tour.Step({
	        target: target,
	        cursorMode: true,
	        targetEvent: function targetEvent() {
	          BX.SidePanel.Instance.open(target.href);
	          setTimeout(function () {
	            return _this.guide.close();
	          }, 1000);
	        }
	      }));
	      this.finish();
	      this.showNextStep();
	    }
	  }, {
	    key: "start",
	    value: function start() {
	      this.projectAddButton.href = main_core.Uri.addParam(this.projectAddButton.href, {
	        PROJECT_OPTIONS: {
	          tourId: this.guide.getId()
	        }
	      });
	      this.showNextStep();
	    }
	  }, {
	    key: "finish",
	    value: function finish() {
	      BX.ajax.runAction('tasks.tourguide.firstprojectcreation.finish');
	    }
	  }, {
	    key: "showNextStep",
	    value: function showNextStep() {
	      var _this2 = this;

	      setTimeout(function () {
	        return _this2.guide.showNextStep();
	      }, 1000);
	    }
	  }]);
	  return FirstProjectCreationTourGuide;
	}();

	var TourGuideController = /*#__PURE__*/function () {
	  function TourGuideController(options) {
	    babelHelpers.classCallCheck(this, TourGuideController);
	    this.tours = options.tours;
	    this.initGuides(options);
	  }

	  babelHelpers.createClass(TourGuideController, [{
	    key: "initGuides",
	    value: function initGuides(options) {
	      if (this.tours.firstProjectCreation.show) {
	        this.firstProjectCreationTourGuide = new FirstProjectCreationTourGuide(options);
	        this.firstProjectCreationTourGuide.start();
	      }
	    }
	  }]);
	  return TourGuideController;
	}();

	var Controller = /*#__PURE__*/function () {
	  function Controller(options) {
	    babelHelpers.classCallCheck(this, Controller);
	    this.membersPopup = new MembersPopup(options);
	    this.filter = new Filter(options);
	    this.tourGuideController = new TourGuideController(options);
	    ActionsController.setOptions(options);
	    this.initPull(options);
	  }

	  babelHelpers.createClass(Controller, [{
	    key: "initPull",
	    value: function initPull(options) {
	      this.pullController = new PullController(options);
	      this.pullClient = pull_client.PULL;
	      this.pullClient.subscribe(this.pullController);
	    }
	  }, {
	    key: "getMembersPopup",
	    value: function getMembersPopup() {
	      return this.membersPopup;
	    }
	  }, {
	    key: "getFilter",
	    value: function getFilter() {
	      return this.filter;
	    }
	  }]);
	  return Controller;
	}();

	exports.Controller = Controller;
	exports.ActionsController = ActionsController;

}((this.BX.Tasks.Projects = this.BX.Tasks.Projects || {}),BX,BX,BX.Main,BX.Event,BX.UI.Tour,BX));
//# sourceMappingURL=script.js.map
