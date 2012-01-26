(function($){
	$.multiplyForms = function(element, options){
		var self = this;
		self.element = $(element);
		self.formCounter = 100;
		self.options = $.extend({}, $.multiplyForms.defaultOptions, options);

		self.element.data("multiplyForms", self);
		self.template = self.element.find("." + self.options.templateClass);

		self.init = function() {

			self.template.find('input, textarea, select, button').attr('disabled', 'disabled');

			// find add links inside self.element
			var addLinks = self.element.find(self.options.addLink);
			// find add links in document
			addLinks = addLinks.length ? addLinks : $(self.options.addLink);

			addLinks.click(function(e) {
				e.preventDefault();
				self._cloneTemplate();
			});

			self.element.find(self.options.deleteLink).live('click', function(e) {
				e.preventDefault();

				var embedForm = $(e.target).parents("." + self.options.embedClass);

				// beforeDelete callback
				var e = jQuery.Event("multiplyForms.delete", {multiplyFormInstance: self});
				e.target = embedForm;
				self.element.trigger(e, [embedForm, self]);
//				if ($.isFunction(self.options.beforeDelete)) {
//					self.options.beforeDelete.call(this, embedForm, self);
//				}
				if (!e.isDefaultPrevented())
					embedForm.remove();
			});
		};

		self._cloneTemplate = function() {
			var self = this;
			var newForm = self.template
				.clone(false)
				.find('input, textarea, select, button')
					.removeAttr('disabled')
				.end();

			if (self.options.mode == "append") {
				newForm.appendTo(self.element);
			} else {
				newForm.appendTo(self.element);
			}

			newForm
				.addClass(self.options.embedClass)
				.removeClass(self.options.templateClass)
				.show();

			self._updateIndex(newForm);


			// afterAdd callback
			var e = jQuery.Event("multiplyForms.add");
			e.target = newForm;
			self.element.trigger(e, [newForm, self]);
//			if ($.isFunction(self.options.afterAdd)) {
//				self.options.afterAdd.call(this, newForm, self);
//			}
		};

		self._updateIndex = function(form) {
			form.find('*[name*="{index}"]').each(function() {
				$(this).attr('name', $(this).attr('name').replace('{index}', self.formCounter));
				this.id = this.id.replace('{index}', self.formCounter);
			});
			self.formCounter++;
		};

		self.init();
	};

	$.multiplyForms.defaultOptions = {
		addLink: ".add",
		deleteLink: ".delete",
		templateClass: "template",
		embedClass: "embed",
		afterAdd: undefined,
		beforeDelete: undefined,
		mode: "append"
	};

	$.fn.multiplyForms = function(options) {
		return this.each(function(){
			(new $.multiplyForms(this, options));
		});
	};

})(jQuery);
