function checkRepeatNews(oForm, oSender)
{
	if(oForm.elements["checkrepeat"] == null || !oForm.elements["checkrepeat"].checked)
        {
                On_DocumentCreateForm_PublishClick(oForm, oSender);
                return false;
        }
		
        var urlRadio = oForm.elements["ref_url_1"];
        for(i=0;i < urlRadio.length;++i)
        {
                if(urlRadio[i].checked && urlRadio[i].value == "outer")
                {
			On_DocumentCreateForm_PublishClick(oForm, oSender);
                        return false;
                } 
        }       
        
        var len = oForm.elements.length;
        var cName = "";
        var cValue = "";
        var cInfo = "";

        for(index=0; index < len; index++ )
        {
                if(oForm.elements[index].value == "Article.Content")
                {
                        cName = oForm.elements[index].name;
                        break;
                }
        }
        
        if(cName == "")
        {
                On_DocumentCreateForm_PublishClick(oForm, oSender);
                return false;
        } 
        
        cName = cName.replace(/_FORM_AP/g,"_FORM_PF");
        cValue = oForm.elements[cName].value;

        if(cValue == "")
        {
                On_DocumentCreateForm_PublishClick(oForm, oSender);
                return false;
        }
        
        cInfo = NEWSDF__getRepeatNews(cValue);

        if(cInfo == "" || cInfo == "NONE" || cInfo.substring(0,5) == "ERROR")
        {
                On_DocumentCreateForm_PublishClick(oForm, oSender);
                return false;
        }
        else
        {
                var modelResult;
		var newsList = new Array();

		newsList = cInfo.split("([RETURN])");

               	modelResult = window.showModalDialog("/gsps/doc_checkrepeat.html", newsList,"dialogWidth=800px");

                if(modelResult==null || modelResult=="STOP" || modelResult=="")
                {
                        return false;
                }
                else if(modelResult=="IGNORE")
                {
                        On_DocumentCreateForm_PublishClick(oForm, oSender);
			return false;
                }
                else
                {
                        var urlRadio = oForm.elements["ref_url_1"];
			var innerUrl = oForm.elements["inner_url_1"];
			var outerUrl = oForm.elements["outer_url_1"];
                        for(i = 0; i < urlRadio.length; ++i)
                        {
                                if(urlRadio[i].value == "outer")
                                {
                                        urlRadio[i].checked = true;
                                        innerUrl.disabled=true;
                                        outerUrl.disabled=false;
                                        outerUrl.value=modelResult;
					break;
                                }
                        }
			On_DocumentCreateForm_PublishClick(oForm, oSender);
			return true;
                }
        }
}
