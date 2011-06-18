/************************************************************************************************************
Ajax poller
Copyright (C) 2006  DTHMLGoodies.com, Alf Magne Kalleland

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

Dhtmlgoodies.com., hereby disclaims all copyright interest in this script
written by Alf Magne Kalleland.

Alf Magne Kalleland, 2006
Owner of DHTMLgoodies.com

************************************************************************************************************/




var serverSideFile = 'ajax-poller-cast-vote-php.php';
var voteLeftImage = 'images/graph_left_1.gif';
var voteRightImage = 'images/graph_right_1.gif';
var voteCenterImage = 'images/graph_middle_1.gif';

var graphMaxWidth = 130;	// It will actually be a little wider than this because of the rounded image at the left and right
var graphMinWidth = 15;	// Minimum size of graph
var pollScrollSpeed = 5;	// Lower = faster
var useCookiesToRememberCastedVotes = false;	// Use cookie to remember casted votes
var txt_totalVotes = 'Общее количество голосов: ';

var ajaxObjects = new Array();
var pollVotes = new Array();
var pollVoteCounted = new Array();
var totalVotes = new Array();


/* Preload images */

var preloadedImages = new Array();
preloadedImages[0] = new Image();
preloadedImages[0].src = voteLeftImage;
preloadedImages[1] = new Image();
preloadedImages[1].src = voteRightImage;
preloadedImages[2] = new Image();
preloadedImages[2].src = voteCenterImage;

/*
These cookie functions are downloaded from
http://www.mach5.com/support/analyzer/manual/html/General/CookiesJavaScript.htm
*/
function Poller_Get_Cookie(name) {
   var start = document.cookie.indexOf(name+"=");
   var len = start+name.length+1;
   if ((!start) && (name != document.cookie.substring(0,name.length))) return null;
   if (start == -1) return null;
   var end = document.cookie.indexOf(";",len);
   if (end == -1) end = document.cookie.length;
   return unescape(document.cookie.substring(len,end));
}
// This function has been slightly modified
function Poller_Set_Cookie(name,value,expires,path,domain,secure) {
	expires = expires * 60*60*24*1000;
	var today = new Date();
	var expires_date = new Date( today.getTime() + (expires) );
    var cookieString = name + "=" +escape(value) +
       ( (expires) ? ";expires=" + expires_date.toGMTString() : "") +
       ( (path) ? ";path=" + path : "") +
       ( (domain) ? ";domain=" + domain : "") +
       ( (secure) ? ";secure" : "");
    document.cookie = cookieString;
}


function showVoteResults(pollId,ajaxIndex)
{

	document.getElementById('poller_waitMessage' + pollId).style.display='none';
    //alert('test');
	var xml = ajaxObjects[ajaxIndex].response;
	xml = xml.replace(/\n/gi,'');

	var reg = new RegExp("^.*?<pollerTitle>(.*?)<.*$","gi");
	var pollerTitle = xml.replace(reg,'$1');

	var resultDiv = document.getElementById('poller_results' + pollId);

	var titleDIV = document.createElement('DIV');
	titleDIV.className='question';
	titleDIV.innerHTML = pollerTitle;
	resultDiv.appendChild(titleDIV);

	var resultUL = document.createElement('UL');
   	resultDiv.appendChild(resultUL);


	var options = xml.split(/<option>/gi);

	pollVotes[pollId] = new Array();
	totalVotes[pollId] = 0;
	for(var no=1;no<options.length;no++){

		var elements = options[no].split(/</gi);
		var currentOptionId = false;
		for(var no2=0;no2<elements.length;no2++){
			if(elements[no2].substring(0,1)!='/'){
				var key = elements[no2].replace(/^(.*?)>.*$/gi,'$1');
				var value = elements[no2].replace(/^.*?>(.*)$/gi,'$1');


				if(key.indexOf('optionText')>=0){
                	var liOption = document.createElement('LI');
    				resultUL.appendChild(liOption);

					var strongOption = document.createElement('STRONG');
					strongOption.innerHTML = value;
					liOption.appendChild(strongOption);

				}

				if(key.indexOf('optionId')>=0){
					currentOptionId = value/1;
				}

				if(key.indexOf('votes')>=0){

					var voteDiv = document.createElement('DIV');
					voteDiv.className='line';
					liOption.appendChild(voteDiv);

					//var leftImage = document.createElement('IMG');
					//leftImage.src = voteLeftImage;
					//voteDiv.appendChild(leftImage);

                    var voteSpan = document.createElement('SPAN');
                    voteSpan.id = 'result_voteTxt' + currentOptionId;
					voteDiv.appendChild(voteSpan);

					var numberDiv = document.createElement('DIV');
					numberDiv.className='result';
					numberDiv.innerHTML = '0%';
					numberDiv.id = 'result_voteTxt_number' + currentOptionId;

					liOption.appendChild(numberDiv);

					//var numberDiv = document.createElement('DIV');
					//numberDiv.style.backgroundImage = 'url(\'' + voteCenterImage + '\')';
					//numberDiv.innerHTML = '0%';
					//numberDiv.id = 'result_voteTxt' + currentOptionId;
					//voteDiv.appendChild(numberDiv);

					//var rightImage = document.createElement('IMG');
					//rightImage.src = voteRightImage;
					//voteDiv.appendChild(rightImage);

					pollVotes[pollId][currentOptionId] = value;
					totalVotes[pollId] = totalVotes[pollId]/1 + value/1;
				}
			}
		}
	}

	var totalVoteP = document.createElement('STRONG');
	totalVoteP.innerHTML = txt_totalVotes + totalVotes[pollId];
	resultUL.appendChild(totalVoteP);

	setPercentageVotes(pollId);
	slideVotes(pollId,0);
}

function setPercentageVotes(pollId)
{
	for(var prop in pollVotes[pollId]){
		pollVotes[pollId][prop] =  Math.round( (pollVotes[pollId][prop] / totalVotes[pollId]) * 100);
	}

	var currentSum = 0;
	for(var prop in pollVotes[pollId]){
		currentSum = currentSum + pollVotes[pollId][prop]/1;
	}
	pollVotes[pollId][prop] = pollVotes[pollId][prop] + (100-currentSum);


}

function slideVotes(pollId,currentPercent)
{
	currentPercent = currentPercent/1 + 1;

	for(var prop in pollVotes[pollId]){
		if(pollVotes[pollId][prop]>=currentPercent){
			var obj = document.getElementById('result_voteTxt' + prop);
			//obj.innerHTML = currentPercent + '%';
			obj.style.width = Math.max(graphMinWidth,Math.round(currentPercent/100*graphMaxWidth)) + 'px';
			var obj2 = document.getElementById('result_voteTxt_number' + prop);
			obj2.innerHTML = currentPercent + '%';

		}
	}

	if(currentPercent<100)setTimeout('slideVotes("' + pollId + '","' + currentPercent + '")',pollScrollSpeed);
}


function prepareForPollResults(pollId)
{
	document.getElementById('poller_waitMessage' + pollId).style.display='block';
	document.getElementById('poller_question' + pollId).style.display='none';
}


function castMyVote(pollId,formObj)
{
	var elements = formObj.elements['vote[' + pollId + ']'];
	var optionId = false;
	for(var no=0;no<elements.length;no++){
		if(elements[no].checked)optionId = elements[no].value;
	}
	Poller_Set_Cookie('dhtmlgoodies_poller_' + pollId,'1',6000000);
	//alert(optionId);
	if(optionId){

		var ajaxIndex = ajaxObjects.length;
		ajaxObjects[ajaxIndex] = new sack();
		ajaxObjects[ajaxIndex].requestFile = serverSideFile + '?pollId=' + pollId + '&optionId=' + optionId;
		prepareForPollResults(pollId);
		ajaxObjects[ajaxIndex].onCompletion = function(){ showVoteResults(pollId,ajaxIndex); };	// Specify function that will be executed after file has been found
		//alert('show');
		ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function

	}
}

function displayResultsWithoutVoting(pollId)
{
	var ajaxIndex = ajaxObjects.length;
	ajaxObjects[ajaxIndex] = new sack();
	ajaxObjects[ajaxIndex].requestFile = serverSideFile + '?pollId=' + pollId;
	prepareForPollResults(pollId);
	ajaxObjects[ajaxIndex].onCompletion = function(){ showVoteResults(pollId,ajaxIndex); };	// Specify function that will be executed after file has been found
	ajaxObjects[ajaxIndex].runAJAX();		// Execute AJAX function


}

