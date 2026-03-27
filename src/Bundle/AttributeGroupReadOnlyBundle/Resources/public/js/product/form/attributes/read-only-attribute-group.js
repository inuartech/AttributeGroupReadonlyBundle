'use strict';
define(['jquery', 'pim/form', 'routing'], function ($, BaseForm, Routing) {
  return BaseForm.extend({
    readOnlyGroupCodes: null,

    initialize: function (config) {
      this.config = config.config;

      BaseForm.prototype.initialize.apply(this, arguments);
    },

    configure: function () {
      this.listenTo(this.getRoot(), 'pim_enrich:form:field:extension:add', this.addFieldExtension);

      this.readOnlyGroupCodes = null;
      this.fetchReadOnlyGroupCodes();

      return BaseForm.prototype.configure.apply(this, arguments);
    },

    fetchReadOnlyGroupCodes: function () {
      if (this.readOnlyGroupCodes !== null) {
        return $.Deferred().resolve(this.readOnlyGroupCodes).promise();
      }

      return $.get(Routing.generate('inuar_attribute_group_readonly_list'))
        .then((codes) => {
          this.readOnlyGroupCodes = Array.isArray(codes) ? codes : [];
        })
        .fail(() => {
          this.readOnlyGroupCodes = [];
        });
    },

    addFieldExtension: function (event) {
      const field = event.field;

      if (!field.attribute || !Array.isArray(this.readOnlyGroupCodes)) {
        return;
      }

      if (!this.readOnlyGroupCodes.includes(field.attribute.group)) {
        return;
      }

      field.setEditable(false);
      this.addReadOnlyFooter(field);
    },

    addReadOnlyFooter: function (field) {
      const $note = $('<span class="AknFieldContainer-validationWarning">')
        .text('This attribute is read-only and cannot be edited.');

      field.addElement('footer', 'read_only_attribute_group', $note);
    },
  });
});
