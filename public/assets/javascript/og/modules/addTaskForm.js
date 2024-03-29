App.modules.addTaskForm = {
  task_lists: {},
  
  /**
   * Show add task form for specific lists
   *
   * @params integer task_list_id
   */
  showAddTaskForm: function(task_list_id) {
    list_details = App.modules.addTaskForm.task_lists[task_list_id];
    if(!list_details) return;
    if(!list_details.can_add_task) return;
    
    // Show this one
    Ext.getDom(list_details.add_task_link_id).style.display = 'none';
    Ext.getDom(list_details.task_form_id).style.display = 'block';
    Ext.getDom(list_details.text_id).focus();
    Ext.getDom(list_details.submit_id).accesskey = 's';
    
    // Hide all forms
    App.modules.addTaskForm.hideAllAddTaskForms(task_list_id);
  }, // showAddTaskForm
  
  /**
   * Hide specific add task form
   *
   * @param integer task_list_id
   */
  hideAddTaskForm: function(task_list_id) {
    list_details = App.modules.addTaskForm.task_lists[task_list_id];
    if(!list_details) return;
    if(!list_details.can_add_task) return;
    if(!Ext.getDom(list_details.text_id)) return;
    
    var el = Ext.getDom(list_details.text_id);
    if (el) el.value = '';
    el = Ext.getDom(list_details.assign_to_id);
    if (el) el.value = '0:0';
    el = Ext.getDom(list_details.add_task_link_id);
    if (el) el.style.display = 'block';
    el = Ext.getDom(list_details.task_form_id);
    if (el) el.style.display = 'none';
    el = Ext.getDom(list_details.submit_id);
    if (el) el.accesskey = '';
  }, // hideAddTaskForm
  
  /**
   * Hide all (except one if ID is provided) add task forms
   *
   * @param integer except_task_list_id Skip this form (if value is provided)
   */
  hideAllAddTaskForms: function(except_task_list_id) {
    var key;
    for(key in App.modules.addTaskForm.task_lists) {
      if(except_task_list_id) {
        if(key != except_task_list_id) App.modules.addTaskForm.hideAddTaskForm(key);
      } else {
        App.modules.addTaskForm.hideAddTaskForm(key);
      } // if
    } // for
  } // hideAllAddTaskForms
  
};