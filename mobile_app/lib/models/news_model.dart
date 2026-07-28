class NewsModel {
  final int id;
  final String title;
  final String slug;
  final String excerpt;
  final String? thumbnailUrl;
  final String? publishedAt;
  final String? author;
  final String? content;

  NewsModel({
    required this.id,
    required this.title,
    required this.slug,
    required this.excerpt,
    this.thumbnailUrl,
    this.publishedAt,
    this.author,
    this.content,
  });

  factory NewsModel.fromJson(Map<String, dynamic> json) {
    return NewsModel(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      title: json['title']?.toString() ?? '',
      slug: json['slug']?.toString() ?? '',
      excerpt: json['excerpt']?.toString() ?? '',
      thumbnailUrl: json['thumbnail_url']?.toString(),
      publishedAt: json['published_at']?.toString(),
      author: json['author']?.toString(),
      content: json['content']?.toString(),
    );
  }
}
