function iniAjax(cb, url, method, data)
{
	var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = 
		function()
		{
			if(xhttp.readyState == 4 && xhttp.status == 200)
			{
				cb(xhttp);				
			}
		};
		xhttp.open(method, url, true);
		if(method == "POST")
		{
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send(data);
		}
		if(method == "GET")
		{
			xhttp.send();
		}
}

function modify_content(contentID)
{
	if(typeof modify_content.beingModified == 'undefined')
	{
		modify_content.beingModified = false;
	}
	var a = event.target.parentElement.parentElement.children;
	if(!modify_content.beingModified)
	{
		for(i=0; i < a.length; i++)
		{
			if(a[i].className == "article_content")
			{
				var b = a[i];
				break;
			}
		}
		var val = $(b).text();
		var str = val.substring(0, val.length - 3);
		$(b).replaceWith("<textarea class=resizable>" + str + "</textarea>");
		modify_content.beingModified = true;
	}
	else
	{
		for(i=0; i < a.length; i++)
		{
			if(a[i].className == "resizable")
			{
				var b = a[i];
				break;
			}
		}
		var content = $(b).val();
		Encoder.EncodeType = "entity";
		content = Encoder.XSSEncode(content, false);
		$(b).replaceWith("<div class=article_content>" + content + "</div>");
		content = Encoder.htmlDecode(content);
		iniAjax(mod_done, "modify.php", "POST", "contentid=" + contentID + "&" + "content=" + content);		
		modify_content.beingModified = false;
	}
}

function delete_content(contentID)
{
	var allClear = confirm("Are you sure you want to delete this post?");
	if(allClear)
		iniAjax(mod_done, "delete.php", "POST", "contentid=" + contentID);
}

function modify_settings()
{
	iniAjax(mod_done, "save_settings.php", "POST", "content=" + $("textarea.resizable").val());
}

function elevate_to_admin(userid)
{
	
}

function mod_done(xhttp)
{
	alert(xhttp.responseText);
}