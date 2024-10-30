const swalAddConfig = {
    content: {
        element: "input",
        attributes: {
            placeholder: classicPlus.placeholder_value
        },
    },
    buttons: [classicPlus.button_cancel, classicPlus.button_confirm],
}

const swalDeleteConfig = {
    title: classicPlus.delete_title,
    icon: "warning",
    buttons: [classicPlus.button_cancel, classicPlus.button_delete],
    dangerMode: true,
}

Vue.directive('sortable', {
    bind: function (el, binding) {
        jQuery(el).sortable({
            handle: '.dashicons-menu',
            cursor: 'move',
            stop: function (event, ui) {
                let ids = [];

                jQuery(ui.item.closest('ul')).children('li').each(function (index, el) {
                    ids.push(jQuery(el).data('id'));
                });

                jQuery(ui.item.closest('tbody')).children('tr').each(function (index, el) {
                    ids.push(jQuery(el).data('id'));
                });

                wp.ajax.post({
                    action: 'classicplus_sort_posts',
                    ids: ids,
                    _ajax_nonce: classicPlus.ajax_nonce
                });
            },
        });
    }
});

new Vue({
    el: '#classicplus-app',
    data: {
        editUrl: classicPlus.edit_url,
        viewUrl: classicPlus.view_url,
        documentUrl: classicPlus.document_url,
        post: classicPlus.post_data.post,
        posts: classicPlus.post_data.posts,
        postStatus: classicPlus.post_status,
    },
    methods: {
        onError: function (error) {
            alert(error);
        },

        addDocs: function () {
            let self = this;

            swal({
                title: classicPlus.enter_document_title,
                ...swalAddConfig
            }).then(inputVal => {
                if (inputVal.trim()) {
                    wp.ajax.send({
                        data: {
                            action: 'classicplus_add_post',
                            title: inputVal.trim(),
                            parent: 0,
                            _ajax_nonce: classicPlus.ajax_nonce
                        },
                        success: function (res) {
                            self.posts.unshift(res);
                        },
                        error: self.onError
                    });
                }
            })
        },

        deleteDocs: function (index, posts) {
            let self = this;

            swal({
                text: classicPlus.delete_document_text,
                ...swalDeleteConfig
            }).then(isTrue => {
                if (isTrue) {
                    self.deletePost(index, posts);
                }
            });
        },

        addSection: function (post) {
            let self = this;

            swal({
                title: classicPlus.enter_section_title,
                ...swalAddConfig
            }).then(inputVal => {
                if (inputVal.trim()) {
                    wp.ajax.send({
                        data: {
                            action: 'classicplus_add_post',
                            title: inputVal.trim(),
                            parent: post.id,
                            order: post.children.length,
                            _ajax_nonce: classicPlus.ajax_nonce
                        },
                        success: function (res) {
                            post.children.push(res);
                        },
                        error: self.onError
                    });
                }
            })
        },

        deleteSection: function (index, posts) {
            let self = this;

            swal({
                text: classicPlus.delete_section_text,
                ...swalDeleteConfig
            }).then(isTrue => {
                if (isTrue) {
                    self.deletePost(index, posts);
                }
            });
        },

        addArticle: function (post, event) {
            let self = this;

            swal({
                title: classicPlus.enter_article_title,
                ...swalAddConfig
            }).then(inputVal => {
                if (inputVal.trim()) {
                    wp.ajax.send({
                        data: {
                            action: 'classicplus_add_post',
                            title: inputVal.trim(),
                            parent: post.id,
                            status: 'draft',
                            order: post.children.length,
                            _ajax_nonce: classicPlus.ajax_nonce
                        },
                        success: function (res) {
                            post.children.push(res);

                            let articles = jQuery(event.target).closest('.section-title').next();

                            if (articles.hasClass('collapsed')) {
                                articles.removeClass('collapsed');
                            }
                        },
                        error: self.onError
                    });
                }
            })
        },

        deleteArticle: function (index, posts) {
            let self = this;

            swal({
                text: classicPlus.delete_article_text,
                ...swalDeleteConfig
            }).then(isTrue => {
                if (isTrue) {
                    self.deletePost(index, posts);
                }
            })
        },

        deletePost: function (index, posts, message) {
            let self = this;
            message = message || classicPlus.post_deleted_text;

            wp.ajax.send({
                data: {
                    action: 'classicplus_delete_post',
                    id: posts[index].id,
                    _ajax_nonce: classicPlus.ajax_nonce
                },
                success: function () {
                    Vue.delete(posts, index);
                    swal(message, '', 'success');
                },
                error: self.onError
            });
        },

        toggleCollapse: function (event) {
            jQuery(event.target).siblings('ul.articles').toggleClass('collapsed');
        }
    },
});
