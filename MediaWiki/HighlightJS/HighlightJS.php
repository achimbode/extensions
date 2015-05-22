<?php
/**
 * HighlightJS extension - wrapper for the highlight.js client-side syntax highlighter
 * - see https://highlightjs.org
 * - usage is the same as GeSHi, e.g. <source lang="html">.....</source>
 *
 * @file
 * @ingroup Extensions
 * @author Aran Dunkley [http://www.organicdesign.co.nz/aran Aran Dunkley]
 * @copyright © 2015 Aran Dunkley
 * @licence GNU General Public Licence 2.0 or later
 * 
 */
if( !defined( 'MEDIAWIKI' ) ) die( 'Not an entry point.' );

$wgHighlightJsStyle = ''; // Set to the name of the preferred style from the highlight/styles directory

define( 'HIGHLIGHTJS_VERSION', '1.0.0, 2015-05-22' );

$wgExtensionCredits['other'][] = array(
	'path'        => __FILE__,
	'name'        => 'HighlightJS',
	'author'      => '[http://www.organicdesign.co.nz/aran Aran Dunkley]',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:HighlightJS',
	'description' => 'Adds syntax highlighting for code blocks using the highlight.js client-side syntax highlighter',
	'version'     => HIGHLIGHTJS_VERSION
);

class HighlightJS {

	function __construct() {
		global $wgExtensionFunctions, $wgHooks;
		$wgExtensionFunctions[] = array( $this, 'setup' );
		$wgHooks['ParserFirstCallInit'][] = $this;
	}

	public function setup() {
		global $wgOut, $wgResourceModules, $wgExtensionAssetsPath, $wgHighlightJsStyle;

		// Set up JavaScript and CSS resources
		$path = $wgExtensionAssetsPath . '/' . basename( __DIR__ );
		$wgResourceModules['ext.highlightjs'] = array(
			'scripts'        => array( 'highlight/highlight.pack.js', 'highlight.js' ),
			'localBasePath'  => __DIR__,
			'remoteBasePath' => $path,
		);
		$wgOut->addModules( 'ext.highlightjs' );
		$css = $wgHighlightJsStyle ? "highlight/styles/$wgHighlightJsStyle.css" : 'highlight.css';
		$wgOut->addStyle( "$path/$css" );
	}

	public function onParserFirstCallInit( Parser $parser ) {
		$parser->setHook( 'source', array( $this, 'source' ) );
		return true;
	}

	public function source( $input, array $args, Parser $parser, PPFrame $frame ) {
		global $wgJsMimeType;
		if( !array_key_exists( 'lang', $args ) ) $args['lang'] = 'nohighlight';
		$class = ' class="' . $args['lang'] . ' todo"';
		$script = "<script type=\"$wgJsMimeType\">if('hljsGo' in window) window.hljsGo();</script>";
		return "<pre><code$class>" . htmlspecialchars( trim( $input ) ) . "</code></pre>$script";
	}
}

new HighlightJS();
