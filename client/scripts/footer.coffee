@timeKeeper = ->

  doTimeTick = ->
    minutes = parseInt(minutes)
    minutes += 1
    displayTime hours, minutes
    return

  doTimeTick()
  timer = setInterval(doTimeTick, 60000)
  return

@refreshAPI = (timeOut) ->
  buttonCooldown timeOut
  executePage true
  return

@buttonCooldown = (timeOut) ->

  doTimeTick = ->
    timeOut -= 1
    $('#cooldown').html timeOut
    if timeOut == 0
      clearInterval timer
      $('#refreshAPI').attr('disabled', false).html 'Force API refresh'
    return

  $('#refreshAPI').attr('disabled', true).html 'Please wait... <span id="cooldown">' + timeOut + '</span>'
  timer = setInterval(doTimeTick, 1000)
  return

@displayTime = (h, m) ->
  hours = parseInt(h)
  minutes = parseInt(m)
  if minutes == 60
    hours += 1
    minutes = 0
  if minutes < 10
    minutes = '0' + minutes
  if hours == 24
    hours = 0
  if hours < 10
    hours = '0' + hours
  $('#EVETime_Hours').html hours
  $('#EVETime_Minutes').html minutes
  return

#Get Character ID from a name
@getCharData = (charName) ->
  $.ajax
    url: 'https://api.eveonline.com/eve/CharacterID.xml.aspx?names=' + charName
    error: (xhr, status, error) ->
      showError 'Character Data', xhr, status, error
      # TODO: implement fancy error logging
      return
    success: (xml) ->
      rows = xml.getElementsByTagName('row')
      i = 0
      while i < rows.length
        row = rows[i]
        charID = row.getAttribute('characterID')
        i++
      getCharDataFromID charID
  return

#Get character info from ID
@getCharDataFromID = (charID) ->
  if parseInt(charID).between(90000000, 100000000, true)
    $.ajax
      url: 'https://api.eveonline.com/eve/CharacterInfo.xml.aspx?characterID=' + charID
      error: (xhr, status, error) ->
        showError 'Character Info', xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        parseCharacterData xml, charID
        return
  else
    $.ajax
      url: 'https://api.eveonline.com/eve/CharacterAffiliation.xml.aspx?ids=' + charID
      error: (xhr, status, error) ->
        showError 'Character Info', xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        parseMiscCharacterData xml, charID
        return

@parseCharacterData = (xml, charID) ->
  charName = undefined
  charRace = undefined
  bloodline = undefined
  ancestry = undefined
  corpName = undefined
  corpDate = undefined
  allianceName = undefined
  allianceDate = undefined
  securityStatus = undefined
  charName = xml.getElementsByTagName('characterName')[0].childNodes[0].nodeValue
  charRace = xml.getElementsByTagName('race')[0].childNodes[0].nodeValue
  bloodline = xml.getElementsByTagName('bloodline')[0].childNodes[0].nodeValue
  ancestry = xml.getElementsByTagName('ancestry')[0].childNodes[0].nodeValue
  corpName = xml.getElementsByTagName('corporation')[0].childNodes[0].nodeValue
  corpID = xml.getElementsByTagName('corporationID')[0].childNodes[0].nodeValue
  corpDate = xml.getElementsByTagName('corporationDate')[0].childNodes[0].nodeValue
  alliance = xml.getElementsByTagName('alliance')[0]
  if alliance
    allianceName = xml.getElementsByTagName('alliance')[0].childNodes[0].nodeValue
    allianceID = xml.getElementsByTagName('allianceID')[0].childNodes[0].nodeValue
    allianceDate = xml.getElementsByTagName('allianceDate')[0].childNodes[0].nodeValue
  securityStatus = xml.getElementsByTagName('securityStatus')[0].childNodes[0].nodeValue
  $('#characterModalTitle').html charName
  $('#character-image').attr 'src', 'https://image.eveonline.com/Character/' + charID + '_128.jpg'
  $('#corporation #corporation-image').attr 'src', "https://image.eveonline.com/Corporation/#{corpID}_64.png"
  $('#corporation #corporation-name').html(corpName)
  $('#corporation #corporation-joined').html("<time class=\"timeago\" datetime=\"#{corpDate}\">#{corpDate}</time>")
  if alliance
    $('#alliance #alliance-image').attr('src', "https://image.eveonline.com/Alliance/#{allianceID}_64.png")
    $('#alliance #alliance-name').html(allianceName)
  $('#info #security-status').html((Math.round( securityStatus * 10 ) / 10).toFixed(1))
  jQuery('time.timeago').timeago()
  modal = document.getElementById('characterInfoWindow')
  modal.style.display = "block"
  return

@parseMiscCharacterData = (xml, charID) ->
  rows = xml.getElementsByTagName('row')
  row = rows[0]
  charName = row.getAttribute('characterName')
  corpName = ''
  if row.getAttribute('corporationID') != 0
    corpName = row.getAttribute('corporationName')
  allianceName = ''
  if row.getAttribute('allianceID')[0] != 0
    allianceName = row.getAttribute('allianceName')
  $('#characterModalTitle').html charName
  $('#characterInfoImage').attr 'src', 'https://image.eveonline.com/Character/' + charID + '_256.jpg'
  charInfo = ''
  if corpName != ''
    charInfo += '<p><strong>Corporation:</strong> ' + corpName + '</p>'
  if allianceName != ''
    charInfo += '<p><strong>Alliance:</strong> ' + allianceName + '</p>'
  $('#characterinfo').html charInfo
  $('#characterModal').modal 'show'
  return

#Show item information
# TODO: implement

#$ ->
#  $('[data-toggle="tooltip"]').tooltip()
#  return
#$ ->
#  $('a').each ->
#    s = window.location.href
#    n = s.indexOf('?')
#    s = s.substring(0, if n != -1 then n else s.length)
#    n = s.indexOf('#')
#    s = s.substring(0, if n != -1 then n else s.length)
#    if $(this).prop('href') == s
#      $(this).addClass 'nav_active'
#      $(this).parent().addClass 'nav_active'
#    return
#  return
currentTime = undefined
keyID = undefined
vCode = undefined
selectedCharacter = undefined
selectedCharacterID = undefined
hours = undefined
minutes = undefined
seconds = undefined
serverOpen = 'False'
onlinePlayers = 0
charIDs = []
charNames = []
$(document).ready ->

  modal = document.getElementById('characterInfoWindow')
  window.onclick = (event) ->
    if event.target == modal
      modal.style.display = 'none'
    return

  doCharInit = ->
#    if document.getElementById('passAlong_keyID') != null
#    keyID = document.getElementById('passAlong_keyID').value
#    vCode = document.getElementById('passAlong_vCode').value
#    selectedCharacter = document.getElementById('passAlong_selectedCharacter').value
    if $.totalStorage('charIDs_' + keyID) == null
      $.ajax
        url: 'https://api.eveonline.com/account/Characters.xml.aspx?keyID=' + keyID + '&vCode=' + vCode
        error: (xhr, status, error) ->
          showError 'Character Init', xhr, status, error
          # TODO: implement fancy error logging
          return
        success: (xml) ->
          rows = xml.getElementsByTagName('row')
          i = 0
          while i < rows.length
            row = rows[i]
            charIDs[i] = row.getAttribute('characterID')
            charNames[i] = row.getAttribute('name')
            i++
          $.totalStorage 'charIDs_' + keyID, charIDs
          $.totalStorage 'charNames_' + keyID, charNames
          processChar charIDs
          characterIDs.resolve()
          return
    else
      charIDs = $.totalStorage('charIDs_' + keyID)
      charNames = $.totalStorage('charNames_' + keyID)
      processChar charIDs
      characterIDs.resolve()
    return

  processChar = (charIDs) ->
    i = 0
    while i < charIDs.length
      css = 'characterInactive'
      if i == selectedCharacter
        css = 'characterActive'
        selectedCharacterID = charIDs[i]
      $('#charLinks').css('visibility', 'visible').append '<li><a id="charLink' + i + '" class="' + css + '" href="?c=' + i + '""><img alt="char' + i + '" id="char' + i + '" class="img" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="50" height="50"></a></li>'
      $('#char' + i).css('visibility', 'visible').attr 'src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_50.jpg'
      $('#charmbl' + i).css('visibility', 'visible').attr 'src', 'https://image.eveonline.com/Character/' + charIDs[i] + '_256.jpg'
      i++
    return

  getServerStatus = ->
    $.ajax
      url: 'https://api.eveonline.com/server/ServerStatus.xml.aspx'
      error: (xhr, status, error) ->
        showError 'Server Status', xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        currentTime = xml.getElementsByTagName('currentTime')[0].childNodes[0].nodeValue
        serverOpen = xml.getElementsByTagName('serverOpen')[0].childNodes[0].nodeValue
        onlinePlayers = xml.getElementsByTagName('onlinePlayers')[0].childNodes[0].nodeValue
        hours = parseInt(currentTime.slice(-8, -6))
        minutes = parseInt(currentTime.slice(-5, -3))
        seconds = parseInt(currentTime.slice(-2))
        displayTime hours, minutes
        setTimeout (->
          timeKeeper hours, minutes, seconds
          return
        ), 60000 - (seconds * 1000)
        serverStatus.resolve()
        return
    return

  characterIDs = $.Deferred()
  serverStatus = $.Deferred()
  doCharInit()
  getServerStatus()
  $.when(characterIDs, serverStatus).done ->
    executePage()
    buttonCooldown 5
    return
  return
