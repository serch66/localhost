
; /* Start:"a:4:{s:4:"full";s:96:"/bitrix/components/bitrix/ui.sidepanel.wrapper/templates/.default/template.min.js?16548425225071";s:6:"source";s:77:"/bitrix/components/bitrix/ui.sidepanel.wrapper/templates/.default/template.js";s:3:"min";s:81:"/bitrix/components/bitrix/ui.sidepanel.wrapper/templates/.default/template.min.js";s:3:"map";s:81:"/bitrix/components/bitrix/ui.sidepanel.wrapper/templates/.default/template.map.js";}"*/
(function(){BX.namespace("BX.UI.SidePanel");if(BX.UI.SidePanel.Wrapper){return}function t(t){}t.prototype.init=function(t){this.container=BX(t.containerId);this.isCloseAfterSave=t.isCloseAfterSave||false;this.isReloadGridAfterSave=t.isReloadGridAfterSave||false;this.isReloadPageAfterSave=t.isReloadPageAfterSave||false;this.skipNotification=t.skipNotification||false;this.useLinkTargetsReplacing=t.useLinkTargetsReplacing||false;this.notification=t.notification||{};var e=BX.SidePanel.Instance.getPreviousSlider(BX.SidePanel.Instance.getSliderByWindow(window));this.parentWindow=e?e.getWindow():top;this.initEditableTitle(t);if(this.isReloadGridAfterSave){if(this.getParam("reloadGridAfterSave")){this.reloadGridOnParentPage()}this.setParam("reloadGridAfterSave",true)}if(this.isCloseAfterSave){if(this.getParam("closeAfterSave")){var i;if(this.notification.content&&top.BX&&!this.getParam("skipNotification")){i=function(){top.BX.loadExt("ui.notification").then(function(){top.BX.UI.Notification.Center.notify(this.notification)}.bind(this))}.bind(this)}else if(this.isReloadPageAfterSave){i=function(){this.parentWindow.location.reload()}.bind(this)}BX.SidePanel.Instance.close(false,i)}if(this.skipNotification){this.setParam("skipNotification",true)}this.setParam("closeAfterSave",true)}if(this.useLinkTargetsReplacing){this.initLinkTargetsReplacing()}};t.prototype.initEditableTitle=function(t){if(!t.title||!t.title.selector||!t.title.defaultTitle){return}var i=this.container.querySelector(t.title.selector);if(!i){return}var n=i.querySelector('input[type="text"]');if(!n){return}i.style.display="none";e.init({dataContainer:i,dataNode:n,defaultTitle:t.title.defaultTitle})};t.prototype.initLinkTargetsReplacing=function(){this.replaceLinkTargets();if(!window.MutationObserver){return}var t=new MutationObserver(this.domMutationHandler.bind(this));t.observe(this.container,{childList:true,subtree:true})};t.prototype.domMutationHandler=function(t){t.forEach(function(t){for(var e=0;e<t.addedNodes.length;++e){var i=t.addedNodes.item(e);if(!i){continue}this.replaceLinkTargets(i)}},this)};t.prototype.replaceLinkTargets=function(t){if(!t){t=document.body}var e=[];if(t.tagName==="A"){e=[t]}else if(t.nodeName!=="#text"){e=BX.convert.nodeListToArray(t.querySelectorAll("a"))}if(e.length===0){return}BX.convert.nodeListToArray(e).filter(function(t){return!t.target}).forEach(function(t){t.target="_top"})};t.prototype.setParam=function(t,e){var i=BX.SidePanel.Instance.getSliderByWindow(window);if(i){i.getData().set(t,e)}};t.prototype.getParam=function(t){var e=BX.SidePanel.Instance.getSliderByWindow(window);if(e){return e.getData().get(t)}return undefined};t.prototype.removeParam=function(t){var e=BX.SidePanel.Instance.getSliderByWindow(window);if(e){e.getData().delete(t)}};t.prototype.reloadGridOnParentPage=function(){var t=this.parentWindow;var e=BX.type.isString(this.isReloadGridAfterSave)?this.isReloadGridAfterSave:null;if(!t.BX.Main||!t.BX.Main.gridManager){return}if(e==="all"){t.BX.Main.gridManager.data.forEach(function(t){t.instance.reload()});return}if(!e&&t.BX.Main.gridManager.data){var i=t.BX.Main.gridManager.data;e=i.length>0?i[0].id:null}if(!e){return}var n=t.BX.Main.gridManager.getById(e);if(!n){return}n.instance.reload()};var e={isInit:false,init:function(t){this.dataNode=t.dataNode;this.titleNode=document.querySelector(".ui-side-panel-wrap-title-name");this.inputNode=document.querySelector(".ui-side-panel-wrap-title-input");this.buttonNode=document.querySelector(".ui-side-panel-wrap-title-edit-button");this.initialTitle=this.titleNode.textContent;this.defaultTitle=t.defaultTitle;BX.bind(this.dataNode,"bxchange",this.onDataNodeChange.bind(this));BX.bind(this.buttonNode,"click",this.startEdit.bind(this));BX.bind(this.inputNode,"keyup",this.onKeyUp.bind(this));BX.bind(this.inputNode,"blur",this.endEdit.bind(this));this.isInit=true;if(!t.disabled){this.enable()}if(!this.dataNode.value){this.dataNode.value=this.defaultTitle}},enable:function(t){t=t||false;if(!this.isInit){return}this.changeDisplay(this.buttonNode,!t);this.titleNode.textContent=!t?this.dataNode.value?this.dataNode.value:this.defaultTitle:this.initialTitle},disable:function(){this.enable(true)},onDataNodeChange:function(){this.titleNode.textContent=this.dataNode.value},onKeyUp:function(t){t=t||window.event;if(t.keyCode===10||t.keyCode===13){this.endEdit();t.preventDefault();return false}},getTitle:function(){var t=this.dataNode.value;if(!t){t=this.titleNode.textContent}return t},startEdit:function(){this.inputNode.value=this.getTitle();this.changeDisplay(this.titleNode,false);this.changeDisplay(this.buttonNode,false);this.changeDisplay(this.inputNode,true);this.inputNode.focus()},endEdit:function(){this.dataNode.value=this.inputNode.value;this.titleNode.textContent=this.inputNode.value;this.changeDisplay(this.inputNode,false);this.changeDisplay(this.buttonNode,true);this.changeDisplay(this.titleNode,true)},changeDisplay:function(t,e){return t.style.display=e?"":"none"}};BX.UI.SidePanel.Wrapper=new t})();
/* End */
;
; /* Start:"a:4:{s:4:"full";s:84:"/bitrix/components/bitrix/ui.toolbar/templates/.default/script.min.js?16548425236984";s:6:"source";s:65:"/bitrix/components/bitrix/ui.toolbar/templates/.default/script.js";s:3:"min";s:69:"/bitrix/components/bitrix/ui.toolbar/templates/.default/script.min.js";s:3:"map";s:69:"/bitrix/components/bitrix/ui.toolbar/templates/.default/script.map.js";}"*/
(function(){"use strict";BX.namespace("BX.UI");BX.UI.ToolbarManager={toolbars:{},create:function(t){var e=new BX.UI.Toolbar(t);if(this.get(e.getId())){throw new Error("The toolbar instance with the same 'id' already exists.")}this.toolbars[e.getId()]=e;return e},getDefaultToolbar:function(){return this.get("default-toolbar")},get:function(t){return t in this.toolbars?this.toolbars[t]:null}};BX.UI.Toolbar=function(t){t=BX.type.isPlainObject(t)?t:{};this.titleMinWidth=BX.type.isNumber(t.titleMinWidth)?t.titleMinWidth:158;this.titleMaxWidth=BX.type.isNumber(t.titleMaxWidth)?t.titleMaxWidth:"";this.filterMinWidth=BX.type.isNumber(t.filterMinWidth)?t.filterMinWidth:300;this.filterMaxWidth=BX.type.isNumber(t.filterMaxWidth)?t.filterMaxWidth:748;this.id=BX.Type.isStringFilled(t.id)?t.id:BX.Text.getRandom();this.toolbarContainer=t.target;if(!BX.Type.isDomNode(this.toolbarContainer)){throw new Error('BX.UI.Toolbar: "target" parameter is required.')}this.titleContainer=this.toolbarContainer.querySelector(".ui-toolbar-title-box");this.filterContainer=this.toolbarContainer.querySelector(".ui-toolbar-filter-box");this.filterButtons=this.toolbarContainer.querySelector(".ui-toolbar-filter-buttons");this.rightButtons=this.toolbarContainer.querySelector(".ui-toolbar-right-buttons");this.afterTitleButtons=this.toolbarContainer.querySelector(".ui-toolbar-after-title-buttons");if(!this.filterContainer){this.filterMinWidth=0;this.filterMaxWidth=0}this.buttons=Object.create(null);this.buttonIds=BX.Type.isArray(t.buttonIds)?t.buttonIds:[];if(!this.buttonIds.length){return}this.buttonIds.forEach(function(t){var e=BX.UI.ButtonManager.createByUniqId(t);if(e){e.getContainer().originalWidth=e.getContainer().offsetWidth;if(!e.getIcon()&&!BX.Type.isStringFilled(e.getDataSet()["toolbarCollapsedIcon"])){if(e.getColor()===BX.UI.ButtonColor.PRIMARY){e.setDataSet({toolbarCollapsedIcon:BX.UI.ButtonIcon.ADD})}else{console.warn('BX.UI.Toolbar: the button "'+e.getText()+'" '+"doesn't have an icon for collapsed mode. "+'Use the "data-toolbar-collapsed-icon" attribute.')}}this.buttons[t]=e}else{console.warn('BX.UI.Toolbar: the button "'+t+"\" wasn't initialized.")}},this);this.windowWidth=document.body.offsetWidth;this.reduceItemsWidth();window.addEventListener("resize",function(){if(this.isWindowIncreased()){this.increaseItemsWidth()}else{this.reduceItemsWidth()}}.bind(this))};BX.UI.Toolbar.prototype={getButtons:function(){return this.buttons},getButton:function(t){return t in this.buttons?this.buttons[t]:null},getId:function(){return this.id},isWindowIncreased:function(){var t=this.windowWidth;var e=document.body.offsetWidth;this.windowWidth=e;return e>t},getContainerSize:function(){return this.toolbarContainer.offsetWidth},getInnerTotalWidth:function(){return this.toolbarContainer.scrollWidth},reduceItemsWidth:function(){if(this.getInnerTotalWidth()<=this.getContainerSize()){return}var t=Object.values(this.getButtons());for(var e=t.length-1;e>=0;e--){var i=t[e];if(!i.getIcon()&&!BX.Type.isStringFilled(i.getDataSet()["toolbarCollapsedIcon"])){continue}if(i.isCollapsed()){continue}i.setCollapsed(true);if(!i.getIcon()){i.setIcon(i.getDataSet()["toolbarCollapsedIcon"])}if(this.getInnerTotalWidth()<=this.getContainerSize()){return}}},increaseItemsWidth:function(){var t=Object.values(this.getButtons());for(var e=0;e<t.length;e++){var i=t[e];var o=i.getContainer();if(!i.isCollapsed()){continue}var n=this.titleMinWidth+this.filterMinWidth+(this.afterTitleButtons?this.afterTitleButtons.offsetWidth:0)+(this.filterButtons?this.filterButtons.offsetWidth:0)+(this.rightButtons?this.rightButtons.offsetWidth:0)+(o.originalWidth-o.offsetWidth);if(n>this.getContainerSize()){break}i.setCollapsed(false);if(i.getIcon()===i.getDataSet()["toolbarCollapsedIcon"]){i.setIcon(null)}}}};BX.UI.Toolbar.Star=function(){this.initialized=false;this.currentPageInMenu=false;this.starContNode=null;BX.ready(function(){this.init()}.bind(this));BX.addCustomEvent("onFrameDataProcessed",function(){this.init()}.bind(this))};BX.UI.Toolbar.Star.prototype={init:function(){this.starContNode=document.getElementById("uiToolbarStar");if(!this.starContNode){return false}if(this.initialized){return false}this.initialized=true;var t=this.starContNode.getAttribute("data-bx-url");if(!BX.type.isNotEmptyString(t)){t=document.location.pathname+document.location.search}t=BX.Uri.removeParam(t,["IFRAME","IFRAME_TYPE"]);top.BX.addCustomEvent("BX.Bitrix24.LeftMenuClass:onSendMenuItemData",function(t){this.processMenuItemData(t)}.bind(this));top.BX.addCustomEvent("BX.Bitrix24.LeftMenuClass:onStandardItemChangedSuccess",function(t){this.onStandardItemChangedSuccess(t)}.bind(this));top.BX.onCustomEvent("UI.Toolbar:onRequestMenuItemData",[{currentFullPath:t,context:window}]);return true},processMenuItemData:function(t){if(t.context&&t.context!==window){return}this.currentPageInMenu=t.currentPageInMenu;if(BX.type.isNotEmptyObject(t.currentPageInMenu)){BX.addClass(this.starContNode,"ui-toolbar-star-active")}this.starContNode.title=BX.message(this.starContNode.classList.contains("ui-toolbar-star-active")?"UI_TOOLBAR_DELETE_PAGE_FROM_LEFT_MENU":"UI_TOOLBAR_ADD_PAGE_TO_LEFT_MENU");if(BX.type.isDomNode(this.currentPageInMenu)&&this.currentPageInMenu.getAttribute("data-type")==="default"){this.starContNode.title=BX.message("UI_TOOLBAR_STAR_TITLE_DEFAULT_PAGE");BX.bind(this.starContNode,"click",function(){this.showMessage(BX.message("UI_TOOLBAR_STAR_TITLE_DEFAULT_PAGE_DELETE_ERROR"))}.bind(this));return true}BX.bind(this.starContNode,"click",function(){var t=document.getElementById("pagetitle").innerText;var e=this.starContNode.getAttribute("data-bx-title-template");if(BX.type.isNotEmptyString(e)){t=e.replace(/#page_title#/i,t)}var i=this.starContNode.getAttribute("data-bx-url");if(!BX.type.isNotEmptyString(i)){i=document.location.pathname+document.location.search}i=BX.Uri.removeParam(i,["IFRAME","IFRAME_TYPE"]);top.BX.onCustomEvent("UI.Toolbar:onStarClick",[{isActive:BX.hasClass(this.starContNode,"ui-toolbar-star-active"),context:window,pageTitle:t,pageLink:i}])}.bind(this))},onStandardItemChangedSuccess:function(t){if(!BX.type.isBoolean(t.isActive)||!this.starContNode){return}if(t.context&&t.context!==window){return}if(t.isActive){this.showMessage(BX.message("UI_TOOLBAR_ITEM_WAS_ADDED_TO_LEFT"));this.starContNode.title=BX.message("UI_TOOLBAR_DELETE_PAGE_FROM_LEFT_MENU");BX.addClass(this.starContNode,"ui-toolbar-star-active")}else{this.showMessage(BX.message("UI_TOOLBAR_ITEM_WAS_DELETED_FROM_LEFT"));this.starContNode.title=BX.message("UI_TOOLBAR_ADD_PAGE_TO_LEFT_MENU");BX.removeClass(this.starContNode,"ui-toolbar-star-active")}},showMessage:function(t){var e=BX.PopupWindowManager.create("left-menu-message",this.starContNode,{content:t,darkMode:true,offsetTop:2,offsetLeft:0,angle:true,events:{onPopupClose:function(){if(e){e.destroy();e=null}}},autoHide:true});e.show();setTimeout(function(){if(e){e.destroy();e=null}},3e3)}}})();
/* End */
;
; /* Start:"a:4:{s:4:"full";s:97:"/bitrix/components/bitrix/rest.marketplace.detail/templates/.default/script.min.js?16548422525266";s:6:"source";s:78:"/bitrix/components/bitrix/rest.marketplace.detail/templates/.default/script.js";s:3:"min";s:82:"/bitrix/components/bitrix/rest.marketplace.detail/templates/.default/script.min.js";s:3:"map";s:82:"/bitrix/components/bitrix/rest.marketplace.detail/templates/.default/script.map.js";}"*/
"use strict";BX.namespace("BX.Rest.Marketplace.Detail");BX.Rest.Marketplace.Detail={init:function(t){t=typeof t==="object"?t:{};this.ajaxPath=t.ajaxPath||null;this.siteId=t.siteId||null;this.appName=t.appName||"";this.appCode=t.appCode||"";this.importUrl=t.importUrl||"";this.openImport=t.openImport||false;this.description=document.querySelector('[data-role="mp-detail-main-description"]');this.descriptionWrapper=document.querySelector('[data-role="mp-detail-main-description-wrapper"]');if(this.openImport===true&&t.importUrl!==""){BX.SidePanel.Instance.open(t.importUrl)}if(BX.type.isDomNode(BX("detail_cont"))){var e=BX("detail_cont").getElementsByClassName("js-employee-install-button");if(BX.type.isDomNode(e[0])){BX.bind(e[0],"click",BX.proxy(function(){this.confirmInstallRequest(BX.proxy_context)},this))}}this.initTabs()},initTabs:function(){this.slicker=document.querySelector('[data-role="mp-detail-content-menu-border"]');this.menuItems=document.querySelectorAll(".mp-detail-content-menu-item");this.menuItemActive;this.contentItems=document.querySelectorAll(".mp-detail-content-wrapper-item");this.contentActiveItem;for(var t=0;t<this.menuItems.length;t++){var e=this.menuItems[t];BX.bind(e,"click",BX.proxy(function(){this.setActiveItem(BX.proxy_context)},this))}this.setSlickerParam()},setSlickerParam:function(){this.menuItemActive=document.querySelector(".mp-detail-content-menu-item-active");this.slicker.style.left=this.menuItemActive.offsetLeft+"px";this.slicker.style.width=this.menuItemActive.offsetWidth+"px"},setActiveItem:function(t){if(BX.hasClass(t,"mp-detail-content-menu-item-active"))return;for(var e=0;e<this.menuItems.length;e++){BX.removeClass(this.menuItems[e],"mp-detail-content-menu-item-active")}BX.addClass(t,"mp-detail-content-menu-item-active");this.setSlickerParam();this.setActiveContainer(t.getAttribute("for"))},setActiveContainer:function(t){this.contentActiveItem=document.getElementById(t);for(var e=0;e<this.contentItems.length;e++){BX.removeClass(this.contentItems[e],"mp-detail-content-wrapper-item-active")}BX.addClass(this.contentActiveItem,"mp-detail-content-wrapper-item-active")},slideDescription:function(){if(!this.description.style.maxHeight){this.description.style.maxHeight=this.descriptionWrapper.offsetHeight+"px"}else{this.description.style.maxHeight=""}},confirmInstallRequest:function(t){var e=BX.PopupWindowManager.create("mp_install_confirm_popup",null,{content:'<div class="mp_install_confirm"><div class="mp_install_confirm_text">'+BX.message("REST_MP_INSTALL_REQUEST_CONFIRM")+"</div></div>",closeByEsc:true,closeIcon:{top:"1px",right:"10px"},buttons:[new BX.PopupWindowButton({text:BX.message("REST_MP_APP_INSTALL_REQUEST"),className:"popup-window-button-accept",events:{click:BX.delegate(function(){e.close();this.sendInstallRequest(t)},this)}}),new BX.PopupWindowButtonLink({text:BX.message("JS_CORE_WINDOW_CANCEL"),className:"popup-window-button-link-cancel",events:{click:function(){this.popupWindow.close()}}})]});e.show()},sendInstallRequest:function(t){BX.PopupWindowManager.create("mp-detail-block",t,{content:BX.message("MARKETPLACE_APP_INSTALL_REQUEST"),angle:{offset:35},offsetTop:8,autoHide:true}).show();BX.ajax({method:"POST",dataType:"json",url:this.ajaxPath,data:{sessid:BX.bitrix_sessid(),site_id:this.siteId,action:"sendInstallRequest",appName:this.appName,appCode:this.appCode},onsuccess:function(){},onfailure:function(){}})}};BX.namespace("BX.Rest.Marketplace.DetailImageScroller");BX.Rest.Marketplace.DetailImageScroller=function(t){this.param=t;this.layout={container:t.target,wrapper:t.target.querySelector(".mp-detail-image-scroller-wrapper"),earLeft:null,earRight:null};this.earTimer=null};BX.Rest.Marketplace.DetailImageScroller.prototype={init:function(){if(!BX.type.isDomNode(this.layout.container))return;this.layout.container.appendChild(this.getEarLeft());this.layout.container.appendChild(this.getEarRight());this.bindEvents();this.adjustEars()},bindEvents:function(){BX.bind(this.layout.wrapper,"scroll",this.adjustEars.bind(this))},getEarLeft:function(){if(this.layout.earLeft)return this.layout.earLeft;return this.layout.earLeft=BX.create("div",{props:{className:"mp-detail-image-scroller-ear mp-detail-image-scroller-ear-left"},events:{mouseenter:this.scrollToLeft.bind(this),mouseleave:this.stopAutoScroll.bind(this)}})},getEarRight:function(){if(this.layout.earRight)return this.scroller.earRight;return this.layout.earRight=BX.create("div",{props:{className:"mp-detail-image-scroller-ear mp-detail-image-scroller-ear-right"},events:{mouseenter:this.scrollToRight.bind(this),mouseleave:this.stopAutoScroll.bind(this)}})},scrollToRight:function(){this.earTimer=setInterval(function(){this.layout.wrapper.scrollLeft+=10}.bind(this),20)},scrollToLeft:function(){this.earTimer=setInterval(function(){this.layout.wrapper.scrollLeft-=10}.bind(this),20)},stopAutoScroll:function(){clearInterval(this.earTimer)},adjustEars:function(){var t=this.layout.wrapper;var e=t.scrollLeft;var i=e>0;var s=t.scrollWidth>Math.round(e+t.offsetWidth);this.layout.container.classList[i?"add":"remove"]("mp-detail-image-scroller-ear-left-shown");this.layout.container.classList[s?"add":"remove"]("mp-detail-image-scroller-ear-right-shown")}};
/* End */
;; /* /bitrix/components/bitrix/ui.sidepanel.wrapper/templates/.default/template.min.js?16548425225071*/
; /* /bitrix/components/bitrix/ui.toolbar/templates/.default/script.min.js?16548425236984*/
; /* /bitrix/components/bitrix/rest.marketplace.detail/templates/.default/script.min.js?16548422525266*/

//# sourceMappingURL=page_d87a688b9d7b9e96cd11058a652653f5.map.js