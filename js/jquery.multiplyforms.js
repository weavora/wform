(function($){
    $.multiplyForms = function(el, options){
        var self = this;

        self.$el = $(el);
        self.el = el;
        self.formCount = 100;

        // Add a reverse reference to the DOM object
        self.$el.data("multiplyForms", self);

        self.init = function(){
            self.options = $.extend({},$.multiplyForms.defaultOptions, options);

			$(self.options.initialForm).find('input').attr('disabled', 'disabled');
			
			$(self.options.addLink).click(function(e) {
				var $newForm = self.$el.find(self.options.initialForm)
					.clone(false)
					.show()
					.find('input')
						.removeAttr('disabled')
					.end()
				.appendTo(self.el);
				self._updateIndex($newForm);
			});

			$(self.options.deleteLink).click(function(e) {
				console.log('delete');
			});
        };

        self._updateIndex = function($form) {
        	var formId = $form.attr('id') + "_" + self.formCount;
        	$form.attr('id', formId);
        	$form.find('*[name*="{index}"]').each(function() {
				this.name = this.name.replace('{index}', self.formCount);
				this.id = this.id.replace('{index}', self.formCount);
			});

			$form.find('.delete').click(function(e) {
				console.log('delete-new');
				$('#'+formId).remove();
			});

			self.formCount++;
        };

        // Sample Function, Uncomment to use
        // self.functionName = function(paramaters){
        //
        // };

        // Run initializer
        self.init();
    };

    $.multiplyForms.defaultOptions = {
    	addLink: ".add-link",
    	deleteLinkClass: "delete",
    	initialForm: ""
    };

    $.fn.multiplyForms = function(options){
        return this.each(function(){
            (new $.multiplyForms(this, options));
        });
    };

})(jQuery);
