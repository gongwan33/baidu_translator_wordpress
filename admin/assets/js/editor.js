(function( window, $, undefined ) {
	'use strict';

	var editor = {
		insert: function( content ) {
			var ed = this.getTinyMce(),
				wpActiveEditor = window.wpActiveEditor;

			if ( false === ed ) {
				return false;
			}

			if ( ed && ! ed.isHidden() ) {
				if ( tinymce.isIE && ed.windowManager.insertimagebookmark ) {
					ed.selection.moveToBookmark( ed.windowManager.insertimagebookmark );
				}

				ed.execCommand( 'mceInsertContent', false, content );
			} else if ( 'undefined' !== typeof QTags ) {
				QTags.insertContent( content );
			} else {
				document.getElementById( wpActiveEditor ).value += content;
			}
		},

		insertShortcode: function( shortcode ) {
			var selection = this.getSelection();

			shortcode = _.extend({
				content: '',
				tag: '',
				type: 'closed'
			}, shortcode );

			if ( selection ) {
				shortcode.content = wp.shortcode.replace( shortcode.tag, selection, function( match ) {
					return match.content;
				});
			}

			this.insert( wp.shortcode.string( shortcode ) );
		},

		getSelection: function() {
			var ed = this.getTinyMce(),
				selection = '',
				wpActiveEditor = window.wpActiveEditor,
				end, start;

			if ( false === ed ) {
				return false;
			}

			if ( ed && ! ed.isHidden() ) {
				selection = ed.selection.getContent();
			} else if ( 'undefined' !== typeof QTags ) {
				ed = QTags.getInstance( wpActiveEditor );
				start = ed.canvas.selectionStart;
				end = ed.canvas.selectionEnd;

				if ( end - start > 0 ) {
					selection = ed.canvas.value.substring( start, end );
				}
			}

			return selection;
		},

		getTinyMce: function() {
			var mce = 'undefined' !== typeof tinymce,
				qt = 'undefined' !== typeof QTags,
				wpActiveEditor = window.wpActiveEditor,
				ed;

			if ( ! wpActiveEditor ) {
				if ( mce && tinymce.activeEditor ) {
					ed = tinymce.activeEditor;
					wpActiveEditor = window.wpActiveEditor = ed.id;
				} else if ( ! qt ) {
					return false;
				}
			} else if ( mce ) {
				if ( tinymce.activeEditor && ( 'mce_fullscreen' === tinymce.activeEditor.id || 'wp_mce_fullscreen' === tinymce.activeEditor.id ) ) {
					ed = tinymce.activeEditor;
				} else {
					ed = tinymce.get( wpActiveEditor );
				}
			}

			return ed;
		}
	};

	$(function( $ ) {
		var $group = $( '.baidu-translator-button-group' );
        $(document).click( function(e) {
			var $group = $( '.baidu-translator-button-group' ).closest( '.baidu-translator-button-group' );
			$group.toggleClass( 'is-open', false );
        });

		$group.on( 'click', '.button', function( e ) {
			var $group = $( this ).closest( '.baidu-translator-button-group' );
			e.preventDefault();
			$group.toggleClass( 'is-open', ! $group.hasClass( 'is-open' ) );
            return false;
		});

		$group.on( 'click', '.baidu-translator-dropdown-menu a', function( e ) {
			var $this = $( this ),
				$group = $this.closest( '.baidu-translator-button-group' ).removeClass( 'is-open' ),
				action = $this.data( 'baidu-translator' );

			e.preventDefault();

			window.wpActiveEditor = $group.data( 'editor' );

			if ( 'insert-plugin' === action ) {
				editor.insert( '[baidu_translator]' );
			} else if ( 'notranslate' === action ) {
				editor.insertShortcode({ tag: 'notranslate' });
			}
		});
	});
})( this, jQuery );
