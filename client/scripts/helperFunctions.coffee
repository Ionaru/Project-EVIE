problems = 0

@getTypeNames = (typeIDsRaw) ->

  ###*
  # @Function to convert typeIDs to TypeNames
  #
  # @param {Array} typeIDsRaw - An array with IDs to convert
  #
  # Pages will collect all typeIDs in a single array and send it to this function at the end of all other processes
  #
  ###
  arraySize = typeIDsRaw.length
  if arraySize > 0

    ###*
    #  Check the array for duplicates and remove them
    ###

    typeIDs = uniq(typeIDsRaw)

    ###*
    # Split the typeIDs array into smaller parts if needed (max 250 in an array)
    # It will recall this function for every array chunk it creates
    ###

    maxSize = 250
    if arraySize > maxSize
      i = 0
      while i < typeIDs.length
        getTypeNames typeIDs.slice(i, i + maxSize)
        i += maxSize

    ###*
    # Convert the array into a long string seperated by commas
    # And cut the last comma
    ###

    typeIDString = ''
    i = 0
    while i < typeIDs.length
      typeIDString += typeIDs[i] + ','
      i++
    typeIDString = typeIDString.substring(0, typeIDString.length - 1)

    ###*
    # Send the request to the EVE Online API servers with the final string (typeIDString) and change html elements to the correct TypeNames
    ###

    $.ajax
      url: 'https://api.eveonline.com/eve/TypeName.xml.aspx?ids=' + typeIDString
      error: (xhr, status, error) ->
        showError 'Get Item Names', xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        `var i`
        rows = xml.getElementsByTagName('row')
        i = 0
        while i < rows.length
          row = rows[i]
          typeID = row.getAttribute('typeID')
          typeName = row.getAttribute('typeName')
          $('[id=' + typeID + ']').html typeName
          i++
        return
  return

@parseTimeRemaining = (now, end, elementID, doTimeTick, expiredMessage) ->
  _day = undefined
  _hour = undefined
  _minute = undefined
  _second = undefined
  d = undefined
  days = undefined
  distance = undefined
  ds = undefined
  h = undefined
  hours = undefined
  hs = undefined
  m = undefined
  minutes = undefined
  ms = undefined
  output = undefined
  s = undefined
  seconds = undefined
  ss = undefined
  timer = undefined

  calculateTime = ->
    output = ''
    _second = 1000
    _minute = _second * 60
    _hour = _minute * 60
    _day = _hour * 24
    days = Math.floor(distance / _day)
    hours = Math.floor(distance % _day / _hour)
    minutes = Math.floor(distance % _hour / _minute)
    seconds = Math.floor(distance % _minute / _second)
    if days > 0
      output += days + ' ' + Pluralize(d, ds, days)
    if hours > 0
      if minutes == 0 and seconds == 0 and days != 0
        output += ' and ' + hours + ' ' + Pluralize(h, hs, hours)
      else if days != 0 and (minutes != 0 or seconds != 0)
        output += ', ' + hours + ' ' + Pluralize(h, hs, hours)
      else
        output += hours + ' ' + Pluralize(h, hs, hours)
    if minutes > 0
      if seconds == 0 and (days != 0 or hours != 0)
        output += ' and ' + minutes + ' ' + Pluralize(m, ms, minutes)
      else if (hours != 0 or days != 0) and (seconds != 0 or hours != 0)
        output += ', ' + minutes + ' ' + Pluralize(m, ms, minutes)
      else
        output += minutes + ' ' + Pluralize(m, ms, minutes)
    if seconds > 0
      output += ' and ' + seconds + ' ' + Pluralize(s, ss, seconds)
    distance -= 1000
    $(elementID).html output
    return

  $(elementID).html 'Calculating Time...'
  try
    now = Date.parse(now.replace(/\-/ig, '/').split('.')[0])
  catch TypeError
  try
    end = Date.parse(end.replace(/\-/ig, '/').split('.')[0])
  catch TypeError
  timer = undefined
  distance = end - now
  if doTimeTick == null
    doTimeTick = false
  if expiredMessage == null
    expiredMessage = '0'
  if distance < 1
    clearInterval timer
    $(elementID).html expiredMessage + '<br>'
    return true
  d = ' day'
  ds = ' days'
  h = ' hour'
  hs = ' hours'
  m = ' minute'
  ms = ' minutes'
  s = ' second'
  ss = ' seconds'
  if doTimeTick
    timer = setInterval(calculateTime, 1000)
  else
    calculateTime()
  return

@Pluralize = (Single, Plural, number) ->
  if number == 1
    Single
  else
    Plural

@Number::formatMoney = (c, d, t) ->
  i = undefined
  j = undefined
  n = undefined
  s = undefined
  if c == null
    c = 2
  if d == null
    d = ','
  if t == null
    t = '.'
  n = this
  s = if n < 0 then '-' else ''
  i = parseInt(n = Math.abs(+n or 0).toFixed(c)) + ''
  j = if (j = i.length) > 3 then j % 3 else 0
  s + (if j then i.substr(0, j) + t else '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (if c then d + Math.abs(n - i).toFixed(c).slice(2) else '')

@Number::between = (a, b, inclusive) ->
  min = Math.min(a, b)
  max = Math.max(a, b)
  if inclusive then this >= min and this <= max else this > min and this < max

@uniq = (a) ->
  seen = {}
  a.filter (item) ->
    if seen.hasOwnProperty(item) then false else (seen[item] = true)

@showError = (module, xhr, status, error) ->
  if $('#alertBox').length == 0
    $('.my-content').prepend '<div id="alertBox" class="alert alert-danger" role="alert"><p><strong>One or more problems were detected while loading this page. :(</strong></p></div>'
  problems++
  response = xhr.status
  errorcode = undefined
  errortext = undefined
  try
    error = xhr.responseXML.getElementsByTagName('error')[0]
    errorcode = error.getAttribute('code')
    errortext = error.childNodes[0].nodeValue
  catch exception
    errorcode = 'Unknown'
    errortext = 'Unknown'
  $('#alertBox').append '<div id="problemBox' + problems + '"><p>- ' + '<a data-toggle="collapse" href="#problem' + problems + '" aria-expanded="false" aria-controls="problem' + problems + '">Problem #' + problems + '</a>' + ' - There was an error in the \'' + module + '\' module.</p>' + '<div class="collapse" id="problem' + problems + '">' + '<hr>' + '<p class="errorText">Problem #' + problems + ' details:<br>' + ' > HTTP response: ' + response + '<br>' + ' > API error code: ' + errorcode + '<br>' + ' > API error text: ' + errortext + '</p>' + '<p>Please search for this issue on the <a target="_blank" href="https://github.com/Ionaru/Project-EVIE/issues?utf8=✓&q=is%3Aissue+' + response + '+' + errorcode + '">issue tracker</a>.</p>' + '<hr>' + '</div></div>'
  return

# Changes XML to JSON

@xmlToJson = (xml) ->
# Create the return object
  obj = {}
  if xml.nodeType == 1
# element
# do attributes
    if xml.attributes.length > 0
      obj['@attributes'] = {}
      j = 0
      while j < xml.attributes.length
        attribute = xml.attributes.item(j)
        obj['@attributes'][attribute.nodeName] = attribute.nodeValue
        j++
  else if xml.nodeType == 3
# text
    obj = xml.nodeValue
  # do children
  if xml.hasChildNodes()
    i = 0
    while i < xml.childNodes.length
      item = xml.childNodes.item(i)
      nodeName = item.nodeName
      if typeof obj[nodeName] == 'undefined'
        obj[nodeName] = xmlToJson(item)
      else
        if typeof obj[nodeName].push == 'undefined'
          old = obj[nodeName]
          obj[nodeName] = []
          obj[nodeName].push old
        obj[nodeName].push xmlToJson(item)
      i++
  obj

@isCacheExpired = (cacheEndTime) ->
  cacheEndTime = Date.parse(cacheEndTime.replace(/\-/ig, '/').split('.')[0])
  cacheEndTime += 3600000
  currentTime = (new Date).getTime()
  distance = cacheEndTime - currentTime
  distance < -5000
