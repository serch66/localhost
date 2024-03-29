(() =>
{
	const defaultOptions = {
		entities: {
			"user": {
				"options": {
					"emailUsers": true,
				},
				"searchable": true,
				"dynamicLoad": true,
				"dynamicSearch": true
			},
			"department": {
				"options": {
					"selectMode": "departmentsOnly",
					"allowFlatDepartments": true
				},
				"searchable": true,
				"dynamicLoad": true,
				"dynamicSearch": true
			},
			"project": {
				"searchable": true,
				"dynamicLoad": true,
				"dynamicSearch": true
			},
			"meta-user": {
				"options":{
					"all-users": {
						"allowView": true
					}
				},
				"searchable": true,
				"dynamicLoad": true,
				"dynamicSearch": false
			},
		}
	}

	const defaultEntities = ['user', 'project', 'department', 'meta-user']

	/**
	 * @class RecipientSelector
	 */
	class RecipientSelector extends EntitySelector
	{
		constructor(context = "BLOG_POST", entities = null)
		{
			super();
			this.singleSelection = false;
			this.entities = entities || defaultEntities
			let provider = new CommonSelectorProvider(context);
			this.setProvider(provider)
			this.setEntitiesOptions(defaultOptions.entities)
		}

		setEntitiesOptions(options) {
			if(typeof options === 'undefined')
				return this;

			let entities = this.entities.reduce((result, entityName)=>{

				result[entityName] = options[entityName]
										? options[entityName]
										: defaultOptions.entities[entityName]

				return result;

			}, {});

			this.provider.setOptions({entities})

			return this;
		}

		onSelectedChanged(data)
		{
			if (this.singleSelection)
			{
				this.ui.close(() => this.onResult(this.provider.prepareResult(data.items)));
			}
		}
	}


	window.RecipientSelector = RecipientSelector
})();
