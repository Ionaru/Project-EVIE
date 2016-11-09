fs = require('fs')
mkdirp = require('mkdirp')

exports.compileStylesheets = (done) ->
  try
    startTime = Date.now()

    ###
      Input and output folders
    ###
    inputDirNameStyle = './client/style/'
    outputDirNameStyle = './client/public/stylesheets/'
    mkdirp(outputDirNameStyle)

    ###
      Compile the style.scss file in the input folder to CSS, this file should have imported all other stylesheets
    ###
    Sass = require('node-sass')
    result = Sass.renderSync(
      file: inputDirNameStyle + 'style.scss'
      outputStyle: 'compressed'
      outFile: outputDirNameStyle + 'style.css'
      sourceMap: DEVMODE
    )

    ###
      Minify the compiled CSS and write a source map if the application is in DEV mode
    ###
    CleanCSS = require('clean-css')
    await new CleanCSS({sourceMap: result.map.toString()}).minify(result.css, defer(error, result))
    if DEVMODE
      fs.writeFileSync(outputDirNameStyle + 'style.css', result.styles.toString() + " /*# sourceMappingURL=style.css.map */")
      fs.writeFileSync(outputDirNameStyle + 'style.css.map', result.sourceMap.toString())
    else
      fs.writeFileSync(outputDirNameStyle + 'style.css', result.styles)

    logger.info("Client-side stylesheets ready, took #{Date.now() - startTime}ms")
  catch e
    logger.error("There was a problem while compiling the stylesheets, stylesheet was not updated.")
    logger.error(e)
  return done()

exports.compileScripts = (done) ->
  try
    startTime = Date.now()

    ###
      Input and output folders
    ###
    inputDirNameJS = './client/scripts/'
    outputDirNameJS = './client/public/javascript/'
    mkdirp(outputDirNameJS)

    Compiler = require('iced-coffee-script-3')
    coffeeFiles = fs.readdirSync(inputDirNameJS)
    if !fs.existsSync(outputDirNameJS)
      fs.mkdirSync(outputDirNameJS)
    fileContent = ''
    for file in coffeeFiles
      fileContent += fs.readFileSync(inputDirNameJS + file, 'utf-8') + '\n'
    fileContentJS = Compiler.compile(fileContent)
    fs.writeFileSync(outputDirNameJS + 'script.js', fileContentJS.toString())

    if PRODMODE
      UglifyJS = require 'uglify-js'
      result = UglifyJS.minify(outputDirNameJS + 'script.js')
      fs.writeFileSync(outputDirNameJS + 'script.js', result.code.toString())

    logger.info("Client-side javascript ready, took #{Date.now() - startTime}ms")
  catch e
    logger.error("There was a problem while compiling the scripts, script was not updated.")
    logger.error(e)
  return done()
