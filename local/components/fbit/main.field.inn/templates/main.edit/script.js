BX.namespace("BX.Crm");

if (typeof(BX.Crm.CreateCompany) === "undefined")
{

    BX.Crm.CreateCompany  = function ()
    {
        this._searchControl = null;
        this._settings = null;
        this.rNode = null;
        this.requisiteAjaxUrl = null;
        this._formNewCompany = null;
        this._items = [];
        this._innfind = false;
    };
    BX.Crm.CreateCompany.prototype =
        {
            initialize: function (params){
                this._settings = params ? params : {};
                this.requisiteAjaxUrl = params['requisiteAjaxUrl'];
                this._searchControl = BX('INN_FIELD');
                this.rNode = BX('CLIENT');

                BX.Crm.RequisiteFieldController.create(
                    'Inn',
                    {
                        countryId: 1,
                        typeId: 'itin',
                        input: this._searchControl,
                        serviceUrl: '/bitrix/components/bitrix/crm.requisite.edit/ajax.php?site=s1&sessid=' + BX.bitrix_sessid(),
                        callbacks: { onFieldsLoad: BX.delegate(this.setupFields, this) , onFieldsLoadCancel: BX.delegate(this.cancelFieldsLoad, this) }
                    }
                );


            },

            cancelFieldsLoad: function(){
                this.searchDoubleINN(this._searchControl.value, '');
                this.clearFields();
                this.onCodeMDMUpdate(this._searchControl.value, '');
            },
            clearFields(){
                let req = this.getSetting('Requsites');
                for(let item in req){
                    let node = {};
                    let elem = document.querySelector("input[name='"+req[item]+"']");
                    if(elem === null){
                        node = BX.create('input', {attrs: {className: 'crm-offer-item-inp', 'name': req[item], "type":"hidden"}});
                    }

                    if(elem !== null){
                        elem.value = '';
                    } else {
                        node.value  = '';
                        this.rNode.appendChild(node);
                    }
                }
            },
            createForm: function(items){
                this._formNewCompany = BX.create('div', {
                        attrs: {
                            className: 'crm-offer-requisite-form-wrap doubleinn',
                        },
                        style: {
                            //display: 'none'
                        },
                        children: [
                            BX.create('div', {
                                attrs: {
                                    className: 'bx-interface-form bx-crm-edit-form'
                                },
                                children: [
                                    BX.create('div', {
                                        attrs: {
                                            className: 'crm-offer-main-wrap'
                                        },
                                        children: this.createFormItems(items)
                                    })
                                ]
                            })
                        ]
                    }
                );
                this.rNode.appendChild(this._formNewCompany);
            },
            createFormItems: function(items){
                let arItems = [];
                if(items.length > 0){
                    arItems.push( BX.create('div', {attrs: {className: 'crm-offer-title'}, text: 'Найдены дубли компаний:'}));
                } else {
                    arItems.push( BX.create('div', {attrs: {className: 'crm-offer-title'}, text: 'Компании нет в базе'}));
                }

                for(let i = 0; i < items.length; i++){
                    let item = BX.create('div', {attrs: {className: 'crm-offer-row'},
                        children: [
                            BX.create('div', {attrs: {className: 'crm-offer-info-label-wrap'}, style: {paddingBottom: '10px',}, text: items[i]}),
                        ]});
                    arItems.push( item );
                }

                return arItems;
            },
            searchDoubleINN: function(INN, KPP)
            {
                let path = '/local/components/fbit/main.field.inn/ajax.php';
                document.querySelectorAll(".doubleinn").forEach((elem) => elem.remove());
                BX.ajax({
                    url: path,
                    data: {
                        'sessid': BX.bitrix_sessid(),
                        'ACTION': 'SEARCH_COMPANY',
                        'searchINN': INN,
                        'searchKPP': KPP,
                        'entityid': this._settings['userField']['ENTITY_VALUE_ID'],
                    },
                    method: 'POST',
                    dataType: 'json',
                    onsuccess: BX.delegate(this.onSearchRequestSuccess, this)
                });
            },
            onCodeMDMUpdate : function(INN, KPP){
                let path = '/local/components/fbit/main.field.inn/ajax.php';
                BX.ajax({
                    url: path,
                    data: {
                        'sessid': BX.bitrix_sessid(),
                        'ACTION': 'CODEMDM_COMPANY_UPDATE',
                        'searchINN': INN,
                        'searchKPP': KPP,
                        'entityid': this._settings['userField']['ENTITY_VALUE_ID'],
                    },
                    method: 'POST',
                    dataType: 'json',
                    onsuccess: BX.delegate(this.onCodemdmUpdateRequestSuccess, this)
                });
            },
            onCodemdmUpdateRequestSuccess: function(results){
                let items = BX.prop.getArray(BX.prop.getObject(results, "data", {}), "items", []);
                if(items.length > 0){
                    this.updateField(items);
                } else {
                    this.createForm(items);
                }

            },
            updateField: function(items){
                let parentNode = this.rNode;
                items.forEach(function(item){
                    for(let key in item){
                        let elem = document.querySelector("input[name='"+key+"']");
                        if(elem === null){
                            node = BX.create('input', {attrs: {className: 'crm-offer-item-inp', 'name': key, "type":"hidden"}});
                        }

                        if(elem !== null){
                            elem.value = item[key];
                        } else {
                            node.value  = item[key];
                            parentNode.appendChild(node);
                        }
                    }
                });
            },
            onSearchRequestSuccess: function(results)
            {
                this._innfind = true;
                this._items = BX.prop.getArray(BX.prop.getObject(results, "data", {}), "items", []);
                if(this._items.length > 0){
                    this.createForm(this._items);
                }
            },
            setupFields: function(field){
                for(var index in field){
                    let name = this.getRequisite(index);
                    if(name){
                        let node = {};
                        let node_ur = {};
                        let elem = document.querySelector("input[name='"+name+"']");
                        if(elem === null){
                            node = BX.create('input', {attrs: {className: 'crm-offer-item-inp', 'name': name, "type":"hidden"}});
                        }

                        let elem_ur = document.querySelector("input[name='"+this.getRequisite('RQ_ADDR_UR')+"']");
                        if(elem_ur === null){
                            node_ur = BX.create('input', {attrs: {className: 'crm-offer-item-inp', 'name': this.getRequisite('RQ_ADDR_UR'), "type":"hidden"}});
                        }

                        switch (index) {
                            case 'RQ_ADDR' :
                                let addr = field[index];
                                let k = Object.keys(addr);
                                let addr_fields = addr[k[k.length - 1]];
                                let addr_array = [];
                                    addr_array.push(addr_fields['POSTAL_CODE']);
                                    addr_array.push(addr_fields['COUNTRY']);
                                    addr_array.push(addr_fields['REGION']);
                                    addr_array.push(addr_fields['CITY']);
                                    addr_array.push(addr_fields['ADDRESS_1']);
                                    addr_array.push(addr_fields['ADDRESS_2']);

                                if(elem !== null){
                                    elem.value = addr_array.join(', ');
                                } else {
                                    node.value  = addr_array.join(', ');
                                }

                                if(elem_ur !== null){
                                    elem_ur.value = addr_array.join(', ');
                                } else {
                                    node_ur.value  = addr_array.join(', ');
                                }

                                break;
                            default:
                                if(elem !== null){
                                    elem.value = field[index];
                                } else {
                                    node.value  = field[index];
                                    this.rNode.appendChild(node);
                                }
                        }

                    }

                }

                this.searchDoubleINN(field['RQ_INN'], field['RQ_KPP']);
                this.onCodeMDMUpdate(field['RQ_INN'], field['RQ_KPP']);
            },
            getRequisite: function(name){
                req = this.getSetting('Requsites');
                return typeof(req[name]) != 'undefined' ? req[name] : '';
            },

            getSetting: function (name, defaultval)
            {
                return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
            },
        };
    BX.Crm.CreateCompany.create = function(params)
    {

        var self = new BX.Crm.CreateCompany();
        self.initialize(params);
        return self;
    };
}
