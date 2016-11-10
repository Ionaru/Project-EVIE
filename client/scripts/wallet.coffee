refTypes = []
amountScrolled = 300

executePage = (refresh = false) ->
  getRefTypes refresh
  getBalance refresh
  return

getRefTypes = (refresh) ->
  data = undefined
  if !$.totalStorage('refIDs') or isCacheExpired($.totalStorage('refIDs')['eveapi']['cachedUntil']['#text']) or refresh
    $.ajax
      url: 'https://api.eveonline.com/eve/RefTypes.xml.aspx'
      error: (xhr, status, error) ->
        showError 'RefType Names', xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        data = xmlToJson(xml)
        rows = data['eveapi']['result']['rowset']['row']
        i = 0
        while i < rows.length
          refTypes[rows[i]['@attributes']['refTypeID']] = rows[i]['@attributes']['refTypeName']
          i++
        $.totalStorage 'refIDs', data
        getWalletJournal refresh
        getWalletTransactions refresh
        return
  else
    data = $.totalStorage('refIDs')
    rows = data['eveapi']['result']['rowset']['row']
    i = 0
    while i < rows.length
      refTypes[rows[i]['@attributes']['refTypeID']] = rows[i]['@attributes']['refTypeName']
      i++
    getWalletJournal refresh
    getWalletTransactions refresh
  return

getBalance = (refresh) ->
  data = undefined
  if !$.totalStorage('characterBalance_' + keyID + selectedCharacterID) or isCacheExpired($.totalStorage('characterBalance_' + keyID + selectedCharacterID)['eveapi']['cachedUntil']['#text']) or refresh
    $.ajax
      url: 'https://api.eveonline.com/char/AccountBalance.xml.aspx?keyID=' + keyID + '&vCode=' + vCode + '&characterID=' + selectedCharacterID
      error: (xhr, status, error) ->
        showError 'Account balance for character ' + selectedCharacterID, xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        data = xmlToJson(xml)
        $.totalStorage 'characterBalance_' + keyID + selectedCharacterID, data
        parseBalance data
        return
  else
    data = $.totalStorage('characterBalance_' + keyID + selectedCharacterID)
    parseBalance data
  return

parseBalance = (data) ->
  balance = data['eveapi']['result']['rowset']['row']['@attributes']['balance']
  options =
    useEasing: false
    useGrouping: true
    separator: '.'
    decimal: ','
    suffix: ' ISK'
  new CountUp('balanceSpan', 0, balance, 2, 1, options).start()
  return

@getWalletJournal = (rowCount, fromID, refresh) ->
  data = undefined
  $('#loadingiconW').html '<i class="fa fa-spin fa-circle-o-notch"></i>'
  url = 'https://api.eveonline.com/char/WalletJournal.xml.aspx?'
  storageName = 'walletJournal_' + keyID + selectedCharacterID
  url += 'keyID=' + keyID
  url += '&vCode=' + vCode
  url += '&characterID=' + selectedCharacterID
  if rowCount
    storageName += '_rc=' + rowCount
    url += '&rowCount=' + rowCount
  else
    storageName += '_rc=50'
    url += '&rowCount=50'
  if fromID
    storageName += '_fid=' + fromID
    url += '&fromID=' + fromID
  if !$.totalStorage(storageName) or isCacheExpired($.totalStorage(storageName)['eveapi']['cachedUntil']['#text']) or refresh
    $.ajax
      url: url
      error: (xhr, status, error) ->
        showError 'Wallet Journal', xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        data = xmlToJson(xml)
        $.totalStorage storageName, data
        parseWalletJournal data
        return
  else
    data = $.totalStorage(storageName)
    parseWalletJournal data
  return

Object.size = (obj) ->
  size = 0
  key = undefined
  for key of obj
    `key = key`
    if obj.hasOwnProperty(key)
      size++
  return size

parseWalletJournal = (data) ->
  rows = data['eveapi']['result']['rowset']['row']
  if Object.size(rows) is 1
    rows = new Array(rows)
  if rows and Object.size(rows) isnt 0
    ownerName1 = undefined
    ownerID1 = undefined
    date = undefined
    amount = undefined
    refTypeID = undefined
    balance = undefined
    refID = undefined
    color = undefined
    i = 0
    while i < Object.size(rows)
      row = rows[i]
      ownerName1 = ''
      color = 'red'
      date = row['@attributes']['date']
      amount = row['@attributes']['amount']
      refTypeID = refTypes[row['@attributes']['refTypeID']]
      balance = row['@attributes']['balance']
      refID = row['@attributes']['refID']
      if amount >= 0
        color = 'green'
        ownerName1 = row['@attributes']['ownerName1']
        ownerID1 = row['@attributes']['ownerID1']
      output = ''
      output += '<tr>'
      output += '<td data-label="Date">' + date + '</td>'
      output += '<td data-label="refType">' + refTypeID + '</td>'
      if ownerName1 != ''
        output += '<td data-label="From">'
        if parseInt(ownerID1).between(90000000, 100000000, true)
          output += '<a class="' + ownerID1 + '" onclick="getCharDataFromID(' + '\'' + ownerID1 + '\'' + ')">' + ownerName1 + '</a>'
        else
          output += '<span class="' + ownerID1 + '">' + ownerName1 + '</span>'
        output += '</td>'
      else
        if $(window).width() > 768
          output += '<td></td>'
      output += '<td style="color:' + color + '" data-label="Amount">' + parseFloat(amount).formatMoney(2, ',', '.') + ' ISK</td>'
      output += '<td data-label="Balance">' + parseFloat(balance).formatMoney(2, ',', '.') + ' ISK</td></tr>'
      output += '</tr>'
      $('#WalletJournalBody').append output
      i++
  if Object.size(rows) is 50
    $('#moreJournal50').attr 'onclick', 'getWalletJournal("50", "' + refID + '")'
    $('#moreJournal100').attr 'onclick', 'getWalletJournal("100", "' + refID + '")'
    $('#moreJournal250').attr 'onclick', 'getWalletJournal("250", "' + refID + '")'
    $('#moreJournal1000').attr 'onclick', 'getWalletJournal("1000", "' + refID + '")'
    $('#moreJournalAll').attr 'onclick', 'getWalletJournal("2560", "' + refID + '")'
  else
    $('#moreJournal').html 'There is no (more) journal info available.'
  $('#loadingiconW').html ''
  return

getWalletTransactions = (rowCount, fromID, refresh) ->
  data = undefined
  $('#loadingiconT').html '<i class="fa fa-spin fa-circle-o-notch"></i>'
  storageName = 'walletTransactions_' + keyID + selectedCharacterID
  url = 'https://api.eveonline.com/char/WalletTransactions.xml.aspx?'
  url += 'keyID=' + keyID
  url += '&vCode=' + vCode
  url += '&characterID=' + selectedCharacterID
  if rowCount
    storageName += '_rc=' + rowCount
    url += '&rowCount=' + rowCount
  else
    storageName += '_rc=50'
    url += '&rowCount=50'
  if fromID
    storageName += '_fid=' + fromID
    url += '&fromID=' + fromID
  if !$.totalStorage(storageName) or isCacheExpired($.totalStorage(storageName)['eveapi']['cachedUntil']['#text']) or refresh
    $.ajax
      url: url
      error: (xhr, status, error) ->
        showError 'Wallet Transactions', xhr, status, error
        # TODO: implement fancy error logging
        return
      success: (xml) ->
        data = xmlToJson(xml)
        $.totalStorage storageName, data
        parseWalletTransactions data
        return
  else
    data = $.totalStorage(storageName)
    parseWalletTransactions data
  return

parseWalletTransactions = (data) ->
  rows = data['eveapi']['result']['rowset']['row']
  if rows and rows.length != 0
    date = undefined
    quantity = undefined
    typeName = undefined
    typeID = undefined
    price = undefined
    clientName = undefined
    transactionType = undefined
    transactionID = undefined
    color = undefined
    info = undefined
    i = 0
    while i < rows.length
      row = rows[i]
      date = row['@attributes']['transactionDateTime']
      quantity = row['@attributes']['quantity']
      typeName = row['@attributes']['typeName']
      typeID = row['@attributes']['typeID']
      price = row['@attributes']['price']
      clientName = row['@attributes']['clientName']
      transactionType = row['@attributes']['transactionType']
      transactionID = row['@attributes']['transactionID']
      if transactionType == 'buy'
        color = 'red'
        info = ' bought from '
      else
        color = 'green'
        info = ' sold to '
      $('#WalletTransactionsBody').append '<tr><td data-label="Date">' + date + '</td><td data-label="Information">' + quantity + ' x <a onclick="getItemData(' + '\'' + typeID + '\'' + ')">' + typeName + '</a>' + info + ' <a onclick="getCharData(' + '\'' + clientName + '\'' + ')">' + clientName + '</a></td><td data-label="Price" style="color: ' + color + '">' + parseFloat(price * quantity).formatMoney(2, ',', '.') + ' ISK (' + parseFloat(price).formatMoney(2, ',', '.') + ' ISK per item)</td></tr>'
      i++
    $('#moreTransactions50').attr 'onclick', 'getWalletTransactions("50", "' + transactionID + '")'
    $('#moreTransactions100').attr 'onclick', 'getWalletTransactions("100", "' + transactionID + '")'
    $('#moreTransactions250').attr 'onclick', 'getWalletTransactions("250", "' + transactionID + '")'
    $('#moreTransactions1000').attr 'onclick', 'getWalletTransactions("1000", "' + transactionID + '")'
    $('#moreTransactionsAll').attr 'onclick', 'getWalletTransactions("2560", "' + transactionID + '")'
  else
    $('#moreTransactions').html 'There is no (more) transaction info available.'
  $('#loadingiconT').html ''
  return

$(window).scroll ->
  if $(window).scrollTop() > amountScrolled
    $('a.back-to-top').fadeIn 'slow'
  else
    $('a.back-to-top').fadeOut 'slow'
  return

document.addEventListener 'DOMContentLoaded', (event) ->
  executePage()
