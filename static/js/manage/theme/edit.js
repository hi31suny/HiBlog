(function($){$.extend({"ajaxCallback":function(rv,success_cb,fail_cb){var auto_alert_error=true;try{var data=$.parseJSON(rv);if(data.code!=100000){if(typeof fail_cb!=="undefined"){auto_alert_error=fail_cb(data);}
if(auto_alert_error){$.alert(data.msg);}}else{if(typeof success_cb!=="undefined"){success_cb(data);}else{$.alert("操作成功",false,function(){location.reload();});}}}catch(e){console.log(e);$.alert(rv);}},"ajaxCallbackDefault":function(response){$.ajaxCallback(response,function(){location.reload();});}});$.fn.extend({"ajaxSubmit":function(success_cb,fail_cb){var form_jq=$(this);form_jq.submit(function(){var method=form_jq.attr("method");var action=form_jq.attr("action");var data=form_jq.serialize();if(!data){$.alert("提交内容为空");}else{$.ajax(action,{"cache":false,"data":data,"type":method,"success":function(o){$.ajaxCallback(o,success_cb,fail_cb);}});}});},"selectAll":function(select_control,child_nodes){var box=$(this);var child_obj=box.find(":checkbox").filter(child_nodes);var control_obj=box.find(select_control);box.delegate(select_control,"click",function(){child_obj.prop("checked",$(this).prop("checked"));});box.delegate(child_nodes,"click",function(){var child_all_size=box.find(child_nodes).size();var child_checked_size=$(child_nodes).filter(":checked").size();if(child_all_size===child_checked_size){control_obj.prop("checked",true);}else{control_obj.prop("checked",false);}});}});})(jQuery);;$(function(){var theme_editor=ace.edit("theme-content");theme_editor.getSession().setMode("ace/mode/html");var $modal_resource=$("#modal-show-resource");$("#theme-unlock").click(function(){var href=$CONFIG.path+"aj/manage/theme/unlock";var theme_id=$(this).data("id");$.post(href,{"id":theme_id},$.ajaxCallbackDefault);});$("#resource-result").delegate("[action-type=show-resource]","click",function(){var $tr=$(this).parents("tr:first");var href=$CONFIG.path+"aj/manage/theme/resource/show";var theme_id=$tr.find("[node-type=id]").html();$.get(href,{"id":theme_id},function(o){$.ajaxCallback(o,function(o){var resource_name=$tr.find("[node-type=resource-name]").html();$modal_resource.find("[node-type=title]").html(resource_name);try{theme_editor.setValue(o.data.content);theme_editor.setReadOnly(o.data.readonly);}catch(e){console.log(e);}
$modal_resource.data("id",theme_id);$modal_resource.modal({"keyboard":false,"backdrop":'static'});});});}).delegate("[action-type=destroy]","click",function(o){var $tr=$(this).parents("tr:first");var resource_name=$tr.find("[node-type=resource-name]").html();$.confirm("确定要删除"+resource_name+"吗?","确认",function(){var resource_id=$tr.find("[node-type=id]").html();var href=$CONFIG.path+"aj/manage/theme/resource/destroy";$.post(href,{"id":resource_id},$.ajaxCallbackDefault);});});$modal_resource.delegate("[action-type=save]","click",function(){var data={"id":$modal_resource.data("id"),"content":theme_editor.getValue()};$.post($CONFIG.path+"aj/manage/theme/save",data,function(o){$.ajaxCallback(o,function(o){$.alert(o.msg);});});});$modal_resource.delegate("[action-type=close]","click",function(){$.confirm("确定要关闭吗？","确认",function(){$modal_resource.modal("hide");})});$("#form-theme-resource-create").ajaxSubmit();});