router = require('express').Router()

router.get '/', (req, res) ->
  return res.render('dashboard', {
    username: req.session.user.username
  })

#router.post '/login', (req, res) ->
#  username = req.body['username']
#  password = req.body['password']
#  return res.render('dashboard', {
#    username: req.session.user.username
#  })
#
#router.post '/temp', (req, res) ->
#  keyID = req.body['keyid']
#  vCode = req.body['vcode']
#  return res.render('dashboard', {
#    username: req.session.user.username
#  })
#
#router.post '/register', (req, res) ->
#  username = req.body['username']
#  email = req.body['email']
#  password = req.body['password']
#  return res.render('apikeys', {
#    username: req.session.user.username
#  })

module.exports = router
