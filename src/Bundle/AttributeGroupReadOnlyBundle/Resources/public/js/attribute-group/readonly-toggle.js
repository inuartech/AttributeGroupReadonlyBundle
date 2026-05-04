'use strict';
define(['jquery', 'underscore', 'oro/translator', 'pim/form', 'routing'], function (
  $,
  _,
  __,
  BaseForm,
  Routing
) {
  const TOGGLE = _.template(`
    <div style="margin: 20px 0 6px 0"><%- label %></div>
    <div class="switch switch-small has-switch" data-on-label="<%- yes %>" data-off-label="<%- no %>" data-flag="<%- flag %>">
      <div class="switch-animate switch-<%= value ? 'on' : 'off' %>">
        <input type="checkbox" <%= value ? 'checked' : '' %> readonly />
        <span class="switch-left switch-small" style="font-size: 13px"><%- yes %></span>
        <label class="switch-small js-toggle-label">&nbsp;</label>
        <span class="switch-right switch-small" style="font-size: 13px"><%- no %></span>
      </div>
    </div>
    <div style="margin-top: 6px; margin-bottom: 16px; color: #a0a0a0; font-size: 12px"><%- helper %></div>
  `);

  return BaseForm.extend({
    state: null,

    initialize: function (config) {
      this.config = config.config;
      BaseForm.prototype.initialize.apply(this, arguments);
    },

    configure: function () {
      this.listenTo(this.getRoot(), 'pim_enrich:form:entity:post_fetch', this.render);
      return BaseForm.prototype.configure.apply(this, arguments);
    },

    render: function () {
      const groupCode = this.getFormData()['code'];
      if (!groupCode) return this;

      $.get(Routing.generate('inuar_attribute_group_readonly_get', {code: groupCode}))
        .then((data) => {
          this.state = {
            frontend_readonly: data.frontend_readonly,
            api_editable:      data.api_editable,
          };

          const yes = __('Yes');
          const no  = __('No');

          this.$el.off('click').html(
            TOGGLE({
              flag:    'frontend_readonly',
              label:   __('inuar_attribute_group_readonly.attribute_group.frontend_readonly.label'),
              helper:  __('inuar_attribute_group_readonly.attribute_group.frontend_readonly.helper'),
              value:   data.frontend_readonly,
              yes, no,
            }) +
            TOGGLE({
              flag:    'api_editable',
              label:   __('inuar_attribute_group_readonly.attribute_group.api_editable.label'),
              helper:  __('inuar_attribute_group_readonly.attribute_group.api_editable.helper'),
              value:   data.api_editable,
              yes, no,
            })
          );

          this.$el.on('click', '.js-toggle-label', this.onToggleClick.bind(this));
        });

      return this;
    },

    onToggleClick: function (e) {
      const $switch  = $(e.currentTarget).closest('.has-switch');
      const flag     = $switch.data('flag');
      const $inner   = $switch.find('.switch-animate');
      const isNowOn  = $inner.hasClass('switch-on');

      $inner.toggleClass('switch-on', !isNowOn).toggleClass('switch-off', isNowOn);
      $switch.find('input[type=checkbox]').prop('checked', !isNowOn);

      this.state[flag] = !isNowOn;

      $.post(Routing.generate('inuar_attribute_group_readonly_save'), {
        attribute_group_code: this.getFormData()['code'],
        frontend_readonly:    this.state.frontend_readonly ? 1 : 0,
        api_editable:         this.state.api_editable ? 1 : 0,
      });
    },
  });
});
