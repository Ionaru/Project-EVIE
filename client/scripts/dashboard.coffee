@executePage = (refresh = false) ->
  $('#CharacterDivs').html ''
  $('#AccountInfo').html ''
  getAccountInfo refresh, keyID, vCode
  i = 0
  while i < charIDs.length
    $('#CharacterDivs').append '' +
        '<div class="col-xs-6 col-sm-4 placeholder">' +
        '<a id="CharacterDivLink' +
        i +
        1 +
        '" style="cursor: pointer;" onclick="getCharDataFromID(' +
        '\'' +
        charIDs[i] +
        '\'' +
        ')">' +
        '<img id="ImageAccount1Character' +
        i +
        1 +
        '" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">' +
        '<h4 class="spacing" id="NameAccount1Character' +
        i +
        1 +
        '"></h4>' +
        '</a>' +
        '<span id="BalanceAccount1Character' +
        i +
        1 +
        '" class="text-muted"></span>' +
        '<p id="SkillAccount1Character' +
        i +
        1 +
        '"></p>' +
        '<div id="countdown"></div>' +
        '</div>'
    $('#NameAccount1Character' + i + 1).html '<strong>' + charNames[i] + '</strong>'
    getBalance refresh, charIDs, i
    getSkillInTraining refresh, charIDs, i
    i++
  i = 0
  while i < charIDs.length
    $('#ImageAccount1Character' + i + 1).attr 'src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_256.jpg'
    i++
  return

@getAccountInfo = (refresh) ->
  if !$.totalStorage('accountInfo_' + keyID) or isCacheExpired($.totalStorage('accountInfo_' + keyID)['eveapi']['cachedUntil']['#text']) or refresh
    $.ajax
      url: 'https://api.eveonline.com/account/AccountStatus.xml.aspx?keyID=' + keyID + '&vCode=' + vCode
      error: (xhr, status, error) ->
        showError 'Account Information', xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        data = xmlToJson(xml)
        $.totalStorage 'accountInfo_' + keyID, data
        parseAccountInfo data
        return
  else
    data = $.totalStorage('accountInfo_' + keyID)
    parseAccountInfo data
  return

@parseAccountInfo = (data) ->
  paidUntil = data['eveapi']['result']['paidUntil']['#text']
  createDate = data['eveapi']['result']['createDate']['#text']
  logonCount = data['eveapi']['result']['logonCount']['#text']
  logonMinutes = data['eveapi']['result']['logonMinutes']['#text']
  $('#AccountInfo').append '' + '<p>Account created on ' + createDate + '</p>' + '<p>Account expires in <span id="accountTime"></span></p>' + '<p>You have logged in ' + logonCount + ' times to the EVE servers</p>' + '<p>Your total play time is <span id="playTime"></span></p>' + '<p>Average session length: ' + Math.round(logonMinutes / logonCount) + ' minutes</p>'
  logonTime = logonMinutes * 60000
  jQuery('time.timeago').timeago()
  parseTimeRemaining currentTime, paidUntil, '#accountTime', true, 'Account expired!'
  parseTimeRemaining 0, logonTime, '#playTime', false, 'No time at all'
  return

@getBalance = (refresh, charIDs, i) ->
  if !$.totalStorage('characterBalance_' + keyID + charIDs[i]) or isCacheExpired($.totalStorage('characterBalance_' + keyID + charIDs[i])['eveapi']['cachedUntil']['#text']) or refresh
    $.ajax
      url: 'https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=' + keyID + '&vCode=' + vCode + '&characterID=' + charIDs[i]
      error: (xhr, status, error) ->
        showError 'Account balance for character ' + charIDs[i], xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        $.totalStorage 'characterBalance_' + keyID + charIDs[i], xmlToJson(xml)
        parseBalance xmlToJson(xml), i
        return
  else
    data = $.totalStorage('characterBalance_' + keyID + charIDs[i])
    parseBalance data, i
  return

@parseBalance = (data, i) ->
  balance = undefined
  balance = data['eveapi']['result']['rowset']['row']['@attributes']['balance']
  $('#BalanceAccount1Character' + i + 1).html '<a href="wallet.php?c=' + i + '">' + parseFloat(balance).formatMoney(2, ',', '.') + ' ISK</a>'
  return

@getSkillInTraining = (refresh, charIDs, i) ->
  if !$.totalStorage('skillInTraining_' + keyID + charIDs[i]) or isCacheExpired($.totalStorage('skillInTraining_' + keyID + charIDs[i])['eveapi']['cachedUntil']['#text']) or refresh
    $.ajax
      url: 'https://api.eveonline.com/char/SkillInTraining.xml.aspx?keyID=' + keyID + '&vCode=' + vCode + '&characterID=' + charIDs[i]
      error: (xhr, status, error) ->
        showError 'Skill training for character ' + charIDs[i], xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        $.totalStorage 'skillInTraining_' + keyID + charIDs[i], xmlToJson(xml)
        parseSkillInTraining xmlToJson(xml), i
        return
  else
    data = $.totalStorage('skillInTraining_' + keyID + charIDs[i])
    parseSkillInTraining data, i
  return

@parseSkillInTraining = (data, i) ->
  if data['eveapi']['result']['trainingTypeID']
    skillIDs = []
    skillID = data['eveapi']['result']['trainingTypeID']['#text']
    skillIDs.push skillID
    skillLvl = data['eveapi']['result']['trainingToLevel']['#text']
    trainingEndTime = data['eveapi']['result']['trainingEndTime']['#text']
    getTypeNames skillIDs
    $('#SkillAccount1Character' + i + 1).html '<a id="skillCharacter' + i + '" href="skills.php?c=' + i + '""><span id="' + skillID + '">Placeholder</span> ' + skillLvl + '</a><br><span id="countdown' + i + '"></span>'
    parseTimeRemaining currentTime, trainingEndTime, '#countdown' + i, true, 'Skill training completed!'
  else
    $('#SkillAccount1Character' + i + 1).html '<a href="skills.php?c=' + i + '">No skill in training</a>'
  return
