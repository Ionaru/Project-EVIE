router = require('express').Router()

router.all '/*', (req, res, next) ->
  logger.debug("[#{req.session.user?.username} (#{req['ip']})] #{req.method} - #{req.url}")
  return next()

# Global router for user info, permissions and cookies
router.get '/*', (req, res, next) ->
  req.session.user = {
    username: 'Guest'
    login: false
  }
  return next()

# Router that handles the logout process
router.all '/logout', (req, res) ->
  # Destroy the user session
  req.session.destroy ->
    # Redirect the user back to the homepage, in case they were on a page with sensitive information
    return res.redirect('/')
  return

module.exports = router
