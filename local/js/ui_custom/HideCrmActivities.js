//Скрываем звонки и почтовые сообщения в таймлайне и на вкладке "История"
var CheckActivityReadPermission = BX.namespace('CheckActivityReadPermission');

CheckActivityReadPermission.GetActivitiesTimeLine = function () {
	if(typeof window.arActivities === 'undefined') window.arActivities = {};

	let calls            = $('.crm-entity-stream-section-call');
	let emails           = $('.crm-entity-stream-section-email');
	let activities       = calls.add(emails);
	let arActivities	 = {};

	if(activities.length > 0) {
		activities.each(function( key ) {
			let userContainer = $(this).find('.crm-entity-stream-content-detail-employee');
			let userId        = userContainer.attr('href').split('user/')[1].replace('/', '');
			if(userId <= 0) return;
			if(typeof window.arActivities[userId] == 'undefined') window.arActivities[userId] = {};
			if(typeof arActivities[userId] == 'undefined') arActivities[userId] = {};
			let checkExist = Object.values(window.arActivities[userId]).find((obj) => { return $(this)[0] == obj[0] });
			if(typeof checkExist !== 'undefined') return;
			window.arActivities[userId][key] = $(this);
			arActivities[userId][key] 		 = $(this);
		});
	}
	return arActivities;
};

CheckActivityReadPermission.GetActivitiesHistoryTab = function () {
	if(typeof window.arActivitiesHistoryTab === 'undefined') window.arActivitiesHistoryTab = {};

	let calls            = $('td.main-grid-cell:contains("звонок")');
	let emails           = $('td.main-grid-cell:contains("исьмо")');
	let activities       = calls.add(emails);
	let arActivities	 = {};

	if(activities.length > 0) {
		activities.each(function( key ) {
			let parentDomElem = $(this).parent();
			let userContainer = parentDomElem.find('.crm-grid-username');
			let userId        = userContainer.attr('href').split('user/')[1].replace('/', '');
			if(userId <= 0) return;
			if(typeof window.arActivitiesHistoryTab[userId] == 'undefined') window.arActivitiesHistoryTab[userId] = {};
			if(typeof arActivities[userId] == 'undefined') arActivities[userId] = {};
			let checkExist = Object.values(window.arActivitiesHistoryTab[userId]).find((obj) => { return parentDomElem[0] == obj[0] });
			if(typeof checkExist !== 'undefined') return;
			window.arActivitiesHistoryTab[userId][key] = parentDomElem;
			arActivities[userId][key] 		 		   = parentDomElem;
		});
	}
	return arActivities;
};

CheckActivityReadPermission.Init = function () {
    $(document).ready(function() {
		let arActivities = CheckActivityReadPermission.GetActivitiesTimeLine();
		if(Object.keys(arActivities).length > 0) CheckActivityReadPermission.CheckHideByUserId(arActivities);
    });
};

CheckActivityReadPermission.CheckHideByUserId = function (arActivities) {
	if(typeof window.CheckActivityReadPermissionUserIds === 'undefined') window.CheckActivityReadPermissionUserIds = {};
    for(let userId in arActivities) {
		if(!window.CheckActivityReadPermissionUserIds[userId]) {
			$.ajax({
				url: '/local/ajax/checkReadCrmActivity.php?userId=' + userId,
				method: 'GET',
				dataType: 'json',
				data: [],
				async: true,
				success: function(result) {
					if(result.result == 'Y') {
						window.CheckActivityReadPermissionUserIds[userId] = 'Y';
						return;
					}
					CheckActivityReadPermissionUserIds[userId] = 'N';
					CheckActivityReadPermission.HideActivities(arActivities[userId]);
				}
			});
		} else if(window.CheckActivityReadPermissionUserIds[userId] == 'N') {
			CheckActivityReadPermission.HideActivities(arActivities[userId]);
		}
    }
};

CheckActivityReadPermission.HideActivities = function (arActivities) {
	for(let key in arActivities) {
		let element = arActivities[key];
		//Для дел в таймлайне
		if(element.hasClass('crm-entity-stream-section')) {
			let titleBlock = element.find('.crm-entity-stream-content-event-title');
			let detail 	   = element.find('.crm-entity-stream-content-detail');
			let title 	   = titleBlock.text();
			if(title.length !== 0) {
				detail.text(title);
			}
			titleBlock.remove();
			element.find('input').remove();
			element.find('.crm-entity-stream-section-context-menu').remove();
			element.find('.crm-entity-stream-content-action-reply-btn').remove();
		} else {
			//Для дел на вкладке "История"
			let indexTd = $('th').index($('th:contains("Описание")'));
			element.children('td').eq(indexTd).html('<span class="main-grid-cell-content">Скрыто</span>');
		}
	}
};

BX.addCustomEvent('onAjaxSuccess', function (event) {
	if(event.HISTORY_ITEMS !== 'undefined') {
		let arActivities = CheckActivityReadPermission.GetActivitiesTimeLine();
		if(Object.keys(arActivities).length > 0) CheckActivityReadPermission.CheckHideByUserId(arActivities);

		let arActivitiesHistory = CheckActivityReadPermission.GetActivitiesHistoryTab();
		if(Object.keys(arActivitiesHistory).length > 0) CheckActivityReadPermission.CheckHideByUserId(arActivitiesHistory);
	}
});
