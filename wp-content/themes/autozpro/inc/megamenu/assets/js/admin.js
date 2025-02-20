( function($) {
	var Autozpro_Modal_Menu_Item = Backbone.View.extend({

		initialize: function( data ) {
			this.model = new Backbone.Model( data );
			this.template = '<span class="autozpro-edit-menu"><i class="fa fa-pencil"></i> Mega</span>';

			this.events = {
				'click' : '_openModalHandler'
			};
		},

		render: function() {
			this.setElement( this.template );
			return this;
		},

		_openModalHandler: function() {
			window.autozpro_menu_modal = new Autozpro_Modal( this.model );
		}

	});

	// Backbone Modal View
	var Autozpro_Modal = Backbone.View.extend({

		initialize: function( model ) {
			this.model = model;

			this.events = {
				'click .close' : '_closeHandler',
				'click .autozpro-modal-overlay' : '_closeHandler',
				'click #edit-megamenu' : '_editSubmenuContentHandler',
				'submit #menu-edit-form' : '_submitEditMenuForm',
				'change .toggle-select-setting select' : 'toggle'
			};

			this.listenTo( this.model, 'destroy', this.remove );
			this.listenTo( this.model, 'change:is_loading', this._reRender.bind( this ) );
			this.listenTo( this.model, 'change:edit_submenu', this._reRender.bind( this ) )

			this.render();

			this.delegateEvents();
		},
		_toggle: function ( el ){
			var val = $( el ).val();
			var target = $( el ).data('target');

			if( target !== undefined ){

				if( val == 1 ){
					$(target).show();
					if( $(target).find( 'select' ).length > 0){
						this._toggle( $(target).find( 'select' ) );
					}
				}else {
					$(target).hide();
					$(target).find( 'select' ).val( 0 );
				}
			}
		},
		toggle:function( e ) {
			this._toggle( e.currentTarget );
		},
		render: function() {
			var that = this;
			this.template = $( '#tpl-autozpro-menu-item-modal' ).html();
			this.template = _.template( this.template, { variable: 'data' } )( this.model.toJSON() );
			this.setElement( this.template );
			$( 'body' ).append( this.el );
			var data = this.model.toJSON();


			data.action = 'autozpro_load_menu_data';
			data.nonce = autozpro_memgamnu_params.nonces.load_menu_data;

			if( data.istop == true ){
				$(".submenu-setting").show();
				$(".submenu-width-setting").show();
			} else {
				$(".submenu-setting").hide();
				$(".submenu-width-setting").hide();
			}

			$(".toggle-select-setting select").each( function() {
				that._toggle( this )
			} );

			if ( this.model.get( 'is_loading' ) === true ) {
				$.ajax({
					type: 'POST',
					url: autozpro_memgamnu_params.ajaxurl,
					data: data,
					beforeSend: function() {

					}
				}).done( function( res ) {
					if ( res.data !== undefined ) {
						_.map( res.data, function( value, name ) {
							that.model.set( name, value );
						} );
					}
					if ( res.status === true ) {
						that.model.set( 'is_loading', false );
					}
				} );
			}

			this.initThirdpary();
			return this;
		},

		_submitEditMenuForm: function( e ) {
			e.preventDefault();
			var that = this;
			var form = $( '#menu-edit-form' );
			var data = form.serializeArray();
			data.push({
				name: 'action',
				value: 'autozpro_update_menu_item_data'
			});

			$.ajax({
				url: autozpro_memgamnu_params.ajaxurl,
				type: 'POST',
				data: data,
				beforeSend: function() {
					form.find( '.autozpro-modal-footer button' ).attr( 'disabled', true );
					form.find( 'button[type="submit"]' ).prepend( '<i class="fa fa-spin fa-spinner"></i> ' );
				}
			}).always( function() {
				form.find( '.autozpro-modal-footer button' ).attr( 'disabled', false );
			} ).done( function( res ) {
				if ( res.status === true ) {
					that.model.destroy();
				}
				if ( res.message !== undefined ) {
					alert( res.message );
				}
			} );

			return false;
		},
		_formatState : function (state) {
			if (!state.id) {
				return state.text;
			}
			var $state = $(
				'<span><i class="' + state.element.value.toLowerCase() + '" ></i> ' + state.text + '</span>'
			);
			return $state;
		},

		initThirdpary: function() {
			if ( this.$( '.icon-picker' ).length > 0 && typeof $.fn.select2 === 'function' ) {
				this.$( '.icon-picker' ).select2( {
					templateResult: this._formatState
				});
			}

			if ( this.$( '.color-picker' ).length > 0 && typeof $.fn.wpColorPicker === 'function' ) {
				this.$( '.color-picker' ).wpColorPicker();
			}
		},

		_reRender: function() {
			this.$el.replaceWith( this.render().el );
		},

		_closeHandler: function(e) {
			e.preventDefault();
			this.model.destroy();
			return false;
		},

		_editSubmenuContentHandler: function(e) {
			this.model.set( 'edit_submenu', true );
		}

	});


	// hover menu-item-handle
	$( document ).on( 'mouseenter', '#menu-to-edit .menu-item-handle', function(e) {
		var menu_item = $( this );
		var menu_title = menu_item.find( '.item-title' );

		var li = menu_item.parents( 'li:first' );
		var menu_id = li.attr( 'id' ).replace( 'menu-item-', '' );
		menu_id = parseInt( menu_id );
		var istop =  $(li).hasClass('menu-item-depth-0');
		menu_title.append( new Autozpro_Modal_Menu_Item({
			menu_id: menu_id,
			is_loading: true,
			istop:istop?1:0
		}).render().el );
	} ).on( 'mouseleave', '#menu-to-edit .menu-item-handle', function(e) {
		var menu_item = $( this );
		var menu_title = menu_item.find( '.item-title' );
		menu_title.find( '.autozpro-edit-menu' ).remove();
	} );
} )(jQuery);
