=== cdnvote ===
Tags: voting, vote, post, poll
Contributors: nakahira
Requires at least: 2.7.1
Tested up to: 2.7.1
Stable tag: 0.4.2

== Description ==

cdnvoteはwordpressの投稿された記事に対して評価を投票する機能を追加するプラグインです。

Add a vote function to wordpress article.
This vote plug in refers to MTVote, and it is made.

== Installation == 

1. Upload cdnvote dirctory to your WordPress /wp-content/plugins/ directory or install through the auto-installer
2. Activate the plugin through the ‘Plugins’ menu in WordPress
3. Please insert a tag of cdnvote displaying a vote form or a result in a template.

show vote form in the post.
　<code>&lt;?php show_cdnvote_form() ?&gt;</code>

show voted count in the post.
　<code>&lt;?php show_cdnvote_count() ?&gt;</code>

show voted average point in the post.
　<code>&lt;?php show_cdnvote_average() ?&gt;</code>

show the 10 post list of high order of the average point. or use widget.
　<code>&lt;?php show_cdnvote_list() ?&gt;</code>
