'use strict';
define(['jquery', 'underscore', 'oro/translator', 'pim/form', 'routing'], function (
  $,
  _,
  __,
  BaseForm,
  Routing
) {
  const TEMPLATE = _.template(`
    <div style="margin: 20px 0 6px 0"><%- label %></div>

    <div class="switch switch-small has-switch" data-on-label="<%- yes %>" data-off-label="<%- no %>">
      <div class="switch-animate switch-<%= isReadOnly ? 'on' : 'off' %>">
        <input id="inuar-readonly-toggle" type="checkbox" <%= isReadOnly ? 'checked' : '' %> readonly />
        <span class="switch-left switch-small" style="font-size: 13px"><%- yes %></span>
        <label class="switch-small js-readonly-label" for="inuar-readonly-toggle">&nbsp;</label>
        <span class="switch-right switch-small" style="font-size: 13px"><%- no %></span>
      </div>
    </div>

    <div style="margin-top: 6px; color: #a0a0a0; font-size: 12px"><%- helper %></div>
  `);

  return BaseForm.extend({
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
      if (!groupCode) {
        return this;
      }

      $.get(Routing.generate('inuar_attribute_group_readonly_get', {code: groupCode}))
        .then((data) => {
          this.$el.off('click').html(TEMPLATE({
            label:      __('inuar_attribute_group_readonly.attribute_group.readonly.label'),
            helper:     __('inuar_attribute_group_readonly.attribute_group.readonly.helper'),
            yes:        __('Yes'),
            no:         __('No'),
            isReadOnly: data.is_read_only,
          }));

          this.$el.on('click', '.js-readonly-label', this.onToggleClick.bind(this));
        });

      return this;
    },

    onToggleClick: function () {
      const groupCode = this.getFormData()['code'];
      const $inner    = this.$el.find('.switch-animate');
      const isNowOn   = $inner.hasClass('switch-on');

      $inner.toggleClass('switch-on', !isNowOn).toggleClass('switch-off', isNowOn);
      this.$el.find('#inuar-readonly-toggle').prop('checked', !isNowOn);

      $.post(Routing.generate('inuar_attribute_group_readonly_save'), {
        attribute_group_code: groupCode,
        is_read_only: !isNowOn ? 1 : 0,
      });
    },
  });
});
