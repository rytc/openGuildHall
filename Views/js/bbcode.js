

function insertAtCaret(areaId,text) {
	$(areaId).val($(areaId).val()+text);
	return false;
}

function a(tagName) {
	strStart = '['+tagName+']';
	strEnd = '[/'+tagName+']';
	if (document.selection) {
		//IE support
		stringBefore = this.value;
		this.focus();
		sel = document.selection.createRange();
		insertstring = sel.text;
		fullinsertstring = strStart + sel.text + strEnd;
		sel.text = fullinsertstring;
		document.selection.empty();
		this.focus();
		stringAfter = this.value;
		i = stringAfter.lastIndexOf(fullinsertstring);
		range = this.createTextRange();
		numlines = stringBefore.substring(0,i).split("\n").length;
		i = i+3-numlines+tagName.length;
		j = insertstring.length;
		range.move("character",i);
		range.moveEnd("character",j);
		range.select();
	}else if (this.selectionStart || this.selectionStart == '0') {
		//MOZILLA/NETSCAPE support
		startPos = this.selectionStart;
		endPos = this.selectionEnd;
		scrollTop = this.scrollTop;
		this.value = this.value.substring(0, startPos) + strStart + this.value.substring(startPos,endPos) + strEnd + this.value.substring(endPos,this.value.length);
		this.focus();
		this.selectionStart = startPos + strStart.length ;
		this.selectionEnd = endPos + strStart.length;
		this.scrollTop = scrollTop;
	} else {
		this.value += strStart + strEnd;
		this.focus();
	}
	
	return false;
}

