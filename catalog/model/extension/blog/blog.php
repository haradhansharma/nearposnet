<?php

class ModelExtensionBlogBlog extends Model {

	public function getArticle($start, $limit){
		return $this->db->query("select a.*,u.*,
			(select count(comment) from ".DB_PREFIX."blog_article_comments where comment_article_id=a.article_id AND comment_parent_id = '0') as countComments
			from ".DB_PREFIX."blog_articles as a LEFT JOIN ".DB_PREFIX. "user as u ON a.user_id=u.user_id WHERE a.status = 'Published' GROUP BY a.article_id  ORDER BY a.article_id DESC LIMIT $start, $limit")->rows;
	}

	public function getArticleData($id)
	{

		$query = $this->db->query("SELECT * FROM ".DB_PREFIX. "blog_articles WHERE article_id = '". (int)$id ."' AND status = 'Published' ");
		return $query->row;

			// return $this->db->table('blog_articles')->find($id)->where('status', 'Published')->get();
	}

	public function getArticles(){
		return $this->db->table('blog_articles')->get();
	}

	public function getLatestArticles($limit)
	{

		$sql = "SELECT * FROM " . DB_PREFIX . "blog_articles WHERE status = 'Published' "; 

		$sql .= " ORDER BY  article_id DESC";

		if (isset($limit)) {
			

			if ($limit < 1) {
				$limit = 20;
			}

			$sql .= " LIMIT " . (int)$limit ;			
		}		

		$query = $this->db->query($sql);

		return $query->rows;


			// return $this->db->table('blog_articles')->where('status', 'Published')->limit($limit)->sortBy('article_id', 'desc')->where('status', 'Published')->get();
	}

	public function getArticlesCategories(){

		return $this->db->query("select c.*,(select count(*) from ".DB_PREFIX."blog_article_category as ac JOIN ".DB_PREFIX."blog_articles as a ON ac.blog_article_id=a.article_id  where blog_category_id=c.category_id AND status ='Published' ) as countArticles from ".DB_PREFIX."blog_categories c WHERE c.blog_category_status ='Active'")->rows;

	}

	public function searchArticles($data)
	{
		return $this->db->query("SELECT * FROM ".DB_PREFIX. "blog_articles WHERE (title LIKE '%$data%' OR description LIKE '%$data%') AND (status ='Published') ")->rows;
	}

	public function filterArticles($data, $start, $limit)
	{
		return $this->db->query("SELECT * FROM ".DB_PREFIX. "blog_articles as a JOIN ".DB_PREFIX. "user as u ON a.user_id = u.user_id WHERE (title LIKE '%$data%' OR description LIKE '%$data%') AND (a.status ='Published') LIMIT $start, $limit ")->rows;
	}

	public function getCategoriesMeta($id)
	{
		$query = $this->db->query("SELECT *, keyword as category_slug FROM " . DB_PREFIX . "blog_categories LEFT JOIN " . DB_PREFIX . "seo_url ON (query = '" . ('blog_category_id=' . $id)  . "' ) WHERE category_id = '" . (int)$id . "' ");

		return $query->row;

			// return $this->db->table('blog_categories')->find($id)->get();
	}

	public function getCategoryName($id)
	{

		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "blog_categories WHERE category_id = '" . (int)$id . "' ");

		return $query->row['name'];
			// return $this->db->table('blog_categories')->find($id)->get('name');
	}

	public function articlesByCategory($id)
	{

		return $this->db->query("select a.*,u.*,
			(select count(comment) from ".DB_PREFIX."blog_article_comments where comment_article_id=a.article_id AND comment_parent_id = '0') as countComments
			from ".DB_PREFIX."blog_articles as a LEFT JOIN ".DB_PREFIX. "user as u ON a.user_id=u.user_id JOIN ".DB_PREFIX. "blog_article_category as ac ON a.article_id = ac.blog_article_id WHERE ac.blog_category_id = $id AND a.status = 'Published'")->rows;
	}

	public function getArticlesByTags($tag, $start, $limit)
	{

		$sql = " SELECT * FROM " . DB_PREFIX . "blog_articles a LEFT JOIN " . DB_PREFIX . "article_tags t ON (a.article_id = t.tag_article_id) LEFT JOIN " . DB_PREFIX . "user u ON (a.user_id = u.user_id)"; 

		$sql .= " WHERE t.tag = '". $tag ."' AND a.status = 'Published'";

		$sql .= " ORDER BY a.article_id DESC";

		if (isset($start) || isset($limit)) {
			if ($start < 0) {
				$start = 0;
			}

			if ($limit < 1) {
				$limit = 20;
			}

			$sql .= " LIMIT " . (int)$start . "," . (int)$limit;			
		}		

		$query = $this->db->query($sql);

		return $query->rows;



			// return $this->db->table('blog_articles a')->join('article_tags t', 'a.article_id', 't.tag_article_id')->join('user u', 'a.user_id', 'u.user_id')->where('t.tag', $tag)->where('status', 'Published')->sortBy('a.article_id', 'desc')->limit($limit)->skip($start)->get();
	}

	public function searchCountSearch($data)
	{
		return $this->db->query("SELECT COUNT('article_id') as count FROM ".DB_PREFIX. "blog_articles WHERE (title LIKE '%$data%' OR description LIKE '%$data%') AND (status ='Published') ")->rows;
	}

	public function countCategory($data)
	{
		return $this->db->query("SELECT COUNT('ac.blog_category_id') as count FROM ".DB_PREFIX. "blog_article_category as ac JOIN ".DB_PREFIX."blog_articles as a ON a.article_id = ac.blog_article_id  WHERE ac.blog_category_id = '$data' AND a.status = 'Published'    ")->rows;
	}
	public function countTags($data)
	{
		return $this->db->query("SELECT COUNT('tag_article_id') as count FROM ".DB_PREFIX. "article_tags as t JOIN ".DB_PREFIX."blog_articles as a ON a.article_id=t.tag_article_id WHERE tag = '$data' AND status ='Published' ")->rows;
	}

	public function getArticlesTotalCount(){

		$query = $this->db->query("SELECT count(*) as total FROM ".DB_PREFIX. "blog_articles WHERE status = 'Published'");
		return $query->row['total'];


		// return $this->db->table('blog_articles')->where('status','Published')->count();
	}

	public function getSingleArticle($article_id){

		$query = $this->db->query("SELECT * FROM ".DB_PREFIX. "blog_articles ba LEFT JOIN ".DB_PREFIX. "user u ON (ba.user_id = u.user_id) WHERE article_id = '". (int)$article_id ."'");
		return $query->row;



		// return $this->db->table('blog_articles ba')->join('user u', 'ba.user_id', 'u.user_id')->find($article_id)->get();
	}

	public function getComments($id, $start, $limit )
	{

		$sql = " SELECT * FROM " . DB_PREFIX . "blog_article_comments"; 

		$sql .= " WHERE comment_article_id = '". (int)$id ."' AND comment_parent_id = '0' AND status = '1'";

		$sql .= " ORDER BY comment_id DESC";

		if (isset($start) || isset($limit)) {
			if ($start < 0) {
				$start = 0;
			}

			if ($limit < 1) {
				$limit = 20;
			}

			$sql .= " LIMIT " . (int)$start . "," . (int)$limit;			
		}		

		$query = $this->db->query($sql);

		return $query->rows;


		// return $this->db->table('blog_article_comments')->where('comment_article_id', $id)->where('comment_parent_id', 0)->where('status', '1')->sortBy('comment_id', 'desc')->limit($limit)->skip($start)->get();
	}

	public function getArticleProducts($id)
	{

		$sql = " SELECT * FROM " . DB_PREFIX . "product p";
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
		$sql .= " LEFT JOIN " . DB_PREFIX . "article_to_products atp ON (p.product_id = atp.product_id)";
		$sql .= " WHERE atp.article_id = '". (int)$id ."'";

		$query = $this->db->query($sql);

		return $query->rows;

		// return $this->db->table('product p')->join('product_description pd', 'p.product_id', 'pd.product_id')->join('article_to_products atp', 'p.product_id', 'atp.product_id')->where('atp.article_id', $id)->get();
	}

	public function countArticleComments($id)
	{
		$sql = " SELECT count(*) as total FROM " . DB_PREFIX . "blog_article_comments";		
		$sql .= " WHERE comment_article_id = '". (int)$id ."' AND comment_parent_id = '0' AND status = '1'";

		$query = $this->db->query($sql);

		return $query->row['total'];

		// return $this->db->table('blog_article_comments')->where('comment_article_id', $id)->where('comment_parent_id', 0)->where('status', '1')->count();
	}
	public function saveComment($data)
	{
		$this->db->query("INSERT INTO " . DB_PREFIX . "blog_article_comments SET customer_name = '" . $this->db->escape($data['customer_name']) . "', comment_article_id = '" . $this->db->escape($data['comment_article_id']) . "', comment = '" . $this->db->escape($data['comment']) . "', status = '" . $this->db->escape($data['status']) . "', comment_date = NOW()");


		// return $this->db->table('blog_article_comments')->add($data);
	}

	public function saveReply($data)
	{

		$this->db->query("INSERT INTO " . DB_PREFIX . "blog_article_comments SET comment_parent_id = '" . $this->db->escape($data['comment_parent_id']) . "', comment_article_id = '" . $this->db->escape($data['comment_article_id']) . "', comment = '" . $this->db->escape($data['comment']) . "', customer_name = '" . $this->db->escape($data['customer_name']) . "', status = '" . $this->db->escape($data['status']) . "', comment_date = NOW()");


		// $this->db->table('blog_article_comments')->add($data);
		// return true;
	}

	public function getReplies($post_id, $comment_id)
	{

		$sql = " SELECT * FROM " . DB_PREFIX . "blog_article_comments";		
		$sql .= " WHERE comment_article_id = '". (int)$post_id ."' AND comment_parent_id = '". (int)$comment_id ."' AND status = '1'";

		$query = $this->db->query($sql);

		return $query->rows;


		// return $this->db->table('blog_article_comments')->where('comment_article_id', $post_id)->where('comment_parent_id', $comment_id)->where('status', '1')->get();
	}	

	public function getBlogCategories()
	{
		$sql = " SELECT * FROM " . DB_PREFIX . "blog_categories";

		// return $this->db->table('blog_categories')->get();
	}

	public function getTags()
	{
		return $this->db->query("SELECT tag_id,tag_article_id,tag, COUNT('tag_id') as countTags FROM ".DB_PREFIX. "article_tags as t JOIN ".DB_PREFIX."blog_articles as a ON t.tag_article_id= a.article_id WHERE a.status = 'Published' GROUP BY tag ORDER BY countTags DESC LIMIT 10 ")->rows;
	}

}

?>	