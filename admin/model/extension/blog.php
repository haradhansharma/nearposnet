<?php

class ModelExtensionBlog extends Model {

	public function getComments($id)

	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_article_comments WHERE comment_post_id = '". $id ."' ");

		return $query->rows;

		// return $this->db->table('blog_article_comments')->where('comment_post_id', $id)->get();
	}

	public function getArticlesList($data = array()){

		$sql = " SELECT * FROM " . DB_PREFIX . "blog_articles WHERE status != 'Trash'"; 

		$sql .= " ORDER BY article_id DESC";


		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];			
		}
		

		$query = $this->db->query($sql);

		return $query->rows;


		// return $this->db->table('blog_articles')->where('status !=','Trash')->limit($data['limit'])->skip($data['start'])->sortBy('article_id', 'desc')->get();

	}

	public function getTrashArticles($start, $limit){

		$sql = " SELECT * FROM " . DB_PREFIX . "blog_articles WHERE status = 'Trash'" ; 

		$sql .= " ORDER BY article_id DESC";

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


		// return $this->db->table('blog_articles')->where('status','Trash')->limit($limit)->skip($start)->sortBy('article_id', 'desc')->get();
	}

	public function getTrashArticlesList(){

		$query = $this->db->query("SELECT article_id FROM " . DB_PREFIX . "blog_articles WHERE status = 'Trash' ");

		return $query->rows;
		// return $this->db->table('blog_articles')->where('status', 'Trash')->get('article_id');
	}

	public function savePost($data)

	{

		$this->db->query("INSERT INTO " . DB_PREFIX . "blog_articles SET title = '" . $this->db->escape($data['title']) . "', blog_image = '" . $this->db->escape($data['blog_image']) . "', description = '" . $this->db->escape($data['description']) . "', meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '" . $this->db->escape($data['meta_description']) . "', meta_keywords = '" . $this->db->escape($data['meta_keywords']) . "', status = '" . $this->db->escape($data['status']) . "', user_id = '" . $this->db->escape($data['user_id']) . "',  created_at = NOW(), modified_at = NOW() ");

		// $this->db->table('blog_articles')->add($data);

		$last_id = $this->db->getlastid();

		if (isset($data['slug'])){

			$store_id= 0;

			$language_id = 1;

			$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'article_id=" . (int)$last_id . "', keyword = '" . $this->db->escape($data['slug']) . "'");
		}

		return $last_id;

	}

	public function copyPost($article_id) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_articles a WHERE a.article_id = '" . $article_id . "'");

		if ($query->num_rows) {

			$data = $query->row;

			$data['article_categories'] = $this->getArticleCategories($article_id);

			$data['article_tags'] = $this->getArticleTagsList($article_id);
			$data['article_products'] = $this->getArticleProducts($article_id);

			$this->addPost($data);

		}

	}

	public function addPost($data)
	{
		if(empty($data['blog_image']))
		{
			$data['blog_image'] = ' ';
		}

		if( empty($data['category_image']))
		{
			$data['category_image'] = ' ';
		}
		$this->db->query("INSERT INTO ".DB_PREFIX."blog_articles  SET title = '". $this->db->escape($data['title']) ."',
			blog_image = '".$data['blog_image']."' ,
			description = '". $this->db->escape($data['description'])."' ,
			 meta_title = '".$this->db->escape($data['meta_title'])."',
			  meta_description = '".$this->db->escape($data['meta_description']) .".',
			   meta_keywords = '".$this->db->escape($data['meta_keywords'])."',
			    slug = '".$this->db->escape($data['slug'])."', status = 'Draft', created_at = NOW(), modified_at = NOW(), user_id = '".$data['user_id']."' ");

		$article_id = $this->db->getlastid();

		if (isset($data['slug'])){
			$store_id= 0;
			$language_id = 1;
			$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'article_id=" . (int)$article_id . "', keyword = '" . $this->db->escape($data['slug']) . "'");
		}

		foreach ($data['article_categories'] as $value) {

			$this->db->query("INSERT INTO ".DB_PREFIX."blog_article_category SET blog_article_id = '".$article_id."', blog_category_id = '".$value['blog_category_id']."'
			");
		}

		foreach ($data['article_tags'] as $value) {

			$this->db->query("INSERT INTO ".DB_PREFIX."article_tags SET tag_article_id = '".$article_id."', tag = '".$value['tag']."'
				");
		}

		foreach ($data['article_products'] as $value) {

			$this->db->query("INSERT INTO ".DB_PREFIX."article_to_products SET article_id = '".$article_id."', product_id = '".$value['product_id']."'
				");
		}

		return $article_id;
	}

	public function savePostCategory($data1)

	{

		$this->db->query("INSERT INTO ".DB_PREFIX."blog_article_category SET blog_article_id = '".$this->db->escape($data1['blog_article_id'])."', blog_category_id = '".$this->db->escape($data1['blog_category_id'])."'
			");

		// return $this->db->table('blog_article_category')->add($data1);

	}

	public function saveArticleProduct($data)

	{

		$this->db->query("INSERT INTO ".DB_PREFIX."article_to_products SET product_id = '".$this->db->escape($data['product_id'])."', article_id = '".$this->db->escape($data['article_id'])."'
			");

		// return $this->db->table('article_to_products')->add($data);

	}

	public function editPost($id)

	{

		$query = $this->db->query(" SELECT *, keyword as slug FROM ".DB_PREFIX."blog_article_category ac LEFT JOIN ".DB_PREFIX."blog_articles a ON (ac.blog_article_id = a.article_id) LEFT JOIN ".DB_PREFIX."blog_categories c ON (ac.blog_category_id = c.category_id) LEFT JOIN " . DB_PREFIX . "seo_url ON (query = '" . ('article_id=' . $id)  . "' ) WHERE ac.blog_article_id = '". (int)$id ."' ");
		return $query->rows;

		// return $this->db->table('blog_article_category ac')->join('blog_articles a','ac.blog_article_id', 'a.article_id')->join('blog_categories c', 'ac.blog_category_id', 'c.category_id')->where('blog_article_id', $id)->get();
	}

	public function trashPost($id, $data)

	{

		$this->db->query("UPDATE ".DB_PREFIX."blog_articles SET status = '".$this->db->escape($data['status'])."' WHERE article_id = '".(int)$id."'
			");



		// return $this->db->table('blog_articles')->where('article_id', $id)->set($data);

	}

	public function restorePost($id, $data)	

	{

		$this->db->query("UPDATE ".DB_PREFIX."blog_articles SET status = '".$this->db->escape($data['status'])."' WHERE article_id = '".(int)$id."'
			");

		// return $this->db->table('blog_articles')->where('article_id', $id)->set($data);

	}

	public function getArticleProducts($id)

	{

		$query = $this->db->query(" SELECT * FROM ".DB_PREFIX."product_description p LEFT JOIN ".DB_PREFIX."article_to_products ap ON (ap.product_id = p.product_id)  WHERE ap.article_id = '". (int)$id ."' ");
		return $query->rows;



		// return $this->db->table('product_description p')->join('article_to_products ap','ap.product_id', 'p.product_id')->where('ap.article_id', $id)->get();

	}

	public function deleteArticleComments($id)

	{

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_article_comments WHERE comment_article_id = '" . (int)$id . "'");

		// return $this->db->table('blog_article_comments')->where('comment_article_id', $id)->delete();

	}

	public function updatePostCategory($data1)

	{

		$this->db->query("INSERT INTO ".DB_PREFIX."blog_article_category SET blog_category_id = '".$this->db->escape($data1['blog_category_id'])."', blog_article_id = '".$this->db->escape($data1['blog_article_id'])."'
			");

		// return $this->db->table('blog_article_category')->add($data1);

	}

	public function updateArticleProducts($data1)

	{
          $this->db->query("INSERT INTO ".DB_PREFIX."article_to_products SET product_id = '".$this->db->escape($data1['product_id'])."', article_id = '".$this->db->escape($data1['article_id'])."'");

		// return $this->db->table('article_to_products')->add($data1);

	}

	public function deletePostCategory($id)

	{

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_article_category WHERE blog_article_id = '" . (int)$id . "'");

		// return $this->db->table('blog_article_category')->where('blog_article_id', $id)->delete();

	}

	public function deleteArticleProducts($id)

	{

		$this->db->query("DELETE FROM " . DB_PREFIX . "article_to_products WHERE article_id = '" . (int)$id . "'");

		// return $this->db->table('article_to_products')->where('article_id', $id)->delete();

	}

	public function updatePost($data,$id)

	{

		$this->db->query("UPDATE " . DB_PREFIX . "blog_articles SET title = '" . $this->db->escape($data['title']) . "', blog_image = '" . $this->db->escape($data['blog_image']) . "', description = '" . $this->db->escape($data['description']) . "', meta_title = '" . $this->db->escape($data['meta_title']) . "', meta_description = '" . $this->db->escape($data['meta_description']) . "', meta_keywords = '" . $this->db->escape($data['meta_keywords']) . "', status = '" . $this->db->escape($data['status']) . "', modified_at = NOW() WHERE article_id = '".(int)$id."' ");



		// $this->db->table('blog_articles')->find($id)->set($data);

		if (isset($data['slug'])){

			return $this->db->query("UPDATE " . DB_PREFIX . "seo_url SET keyword = '" . $this->db->escape($data['slug']) . "' WHERE query = 'article_id=" . (int)$id . "'");
		}

	}

	public function deletePost($id)

	{

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_articles WHERE article_id = '" . (int)$id . "'");

		 // $this->db->table('blog_articles')->find($id)->delete();

		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'article_id=" . (int)$id . "'");

	}

	public function countArticles(){

		$query = $this->db->query(" SELECT count(*) as total FROM " . DB_PREFIX . "blog_articles WHERE status != 'Trash' ");

		return $query->row['total'];

		// return $this->db->table('blog_articles')->where('status !=', 'Trash')->count();

	}

	public function getArticles($data = array())

	{

		$sql = "SELECT a.*,c.*,ac.*,at.* FROM " . DB_PREFIX . "blog_articles a LEFT JOIN " . DB_PREFIX . "blog_article_category ac ON (a.article_id = ac.blog_article_id) LEFT JOIN ".DB_PREFIX."article_tags at ON (a.article_id = at.tag_article_id) LEFT JOIN ".DB_PREFIX."blog_categories c ON(ac.blog_category_id = c.category_id) WHERE a.status !='Trash' ";

		if(!empty($data)){

			if (!empty($data['filter_title'])) {

				$sql .= " AND  a.title LIKE '" . $this->db->escape($data['filter_title']) . "%'";

			}

			if (!empty($data['filter_category'])) {

				$sql .= " AND  c.name LIKE '" . $this->db->escape($data['filter_category']) . "%'";

			}

			if (!empty($data['filter_tag'])) {

				$sql .= " AND at.tag LIKE '" . $this->db->escape($data['filter_tag']) . "%'";

			}

			if (!empty($data['filter_status'])) {

				$sql .= " AND a.status LIKE '" . $this->db->escape($data['filter_status']) . "%'";

			}

		}

		$sql .= " GROUP BY ac.blog_article_id";
		$sort_data = array(

			'a.title',

			'at.tag',

			'c.name',

			'a.status',

			'a.created_at'

		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {

			$sql .= " ORDER BY " . $data['sort'];

		} else {

			$sql .= " ORDER BY a.title";

		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {

			$sql .= " DESC";

		} else {

			$sql .= " ASC";

		}

		if (isset($data['start']) || isset($data['limit'])) {

			if ($data['start'] < 0) {

				$data['start'] = 0;

			}

			if ($data['limit'] < 1) {

				$data['limit'] = 10;

			}



			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];

		}

		$query = $this->db->query($sql);

		return $query->rows;

	}

	public function getTrashArticlesCount()

	{

		$query = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "blog_articles");

		return $query->row['total'];

		// return $this->db->table('blog_articles')->where('status','Trash')->count();

	}

	//categories functions

	public function getCategoryTotalCount(){

		$query = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "blog_categories");

		return $query->row['total'];

		// return $this->db->table('blog_categories')->count();

	}

	public function getCategoriesLists($start, $limit){

		$sql = " SELECT * FROM " . DB_PREFIX . "blog_categories"; 

		$sql .= " ORDER BY category_id DESC";

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

		// return $this->db->table('blog_categories')->sortBy('category_id', 'DESC')->limit($limit)->skip($start)->get();

	}

	public function getCategoriesList(){

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_categories ");

		return $query->rows;

		// return $this->db->table('blog_categories')->get();

	}

	public function getActiveCategories(){


		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blog_categories WHERE blog_category_status = 'Active' ");

		return $query->rows;

		// return $this->db->table('blog_categories')->where('blog_category_status', 'Active')->get();

	}

	public function saveCategory($data)

	{

		$this->db->query("INSERT INTO " . DB_PREFIX . "blog_categories SET name = '" . $this->db->escape($data['name']) . "', category_description = '" . $this->db->escape($data['category_description']) . "', category_image = '" . $this->db->escape($data['category_image']) . "', meta_title_category = '" . $this->db->escape($data['meta_title_category']) . "', meta_description_category = '" . $this->db->escape($data['meta_description_category']) . "', meta_keywords_category = '" . $this->db->escape($data['meta_keywords_category']) . "', blog_category_status = '" . $this->db->escape($data['blog_category_status']) . "', created_at = NOW(), modified_at = NOW()");

	 	// $this->db->table('blog_categories')->add($data);

		$last_category_id = $this->db->getlastid();

		

		if (isset($data['category_slug'])){

		$store_id= 0;

		$language_id = 1;

		$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'blog_category_id=" . (int)$last_category_id . "', keyword 	 = '" . $this->db->escape($data['category_slug']) . "'");

		}

		return $last_category_id;

	}

	public function editCategory($id)

	{

		
$query = $this->db->query("SELECT *, keyword as category_slug FROM " . DB_PREFIX . "blog_categories LEFT JOIN " . DB_PREFIX . "seo_url ON (query = '" . ('blog_category_id=' . $id)  . "' ) WHERE category_id = '" . (int)$id . "' ");

		return $query->row;

		// return $this->db->table('blog_categories')->find($id)->get();

	}

	public function updateCategory($data,$id)

	{

		$this->db->query("UPDATE " . DB_PREFIX . "blog_categories SET name = '" . $this->db->escape($data['name']) . "', category_description = '" . $this->db->escape($data['category_description']) . "', category_image = '" . $this->db->escape($data['category_image']) . "', meta_title_category = '" . $this->db->escape($data['meta_title_category']) . "', meta_description_category = '" . $this->db->escape($data['meta_description_category']) . "', meta_keywords_category = '" . $this->db->escape($data['meta_keywords_category']) . "', blog_category_status = '" . $this->db->escape($data['blog_category_status']) . "', modified_at = '" . $this->db->escape($data['modified_at']) . "' WHERE category_id = '".(int)$id."' ");

		// $this->db->table('blog_categories')->find($id)->set($data);

		if (isset($data['category_slug'])){

			return $this->db->query("UPDATE " . DB_PREFIX . "seo_url SET keyword = '" . $this->db->escape($data['category_slug']) . "' WHERE query = 'blog_category_id=" . (int)$id . "'");
		}

	}

	public function deleteCategory($id)

	{

		 // $this->db->table('blog_categories')->find($id)->delete();
		 $this->db->query("DELETE FROM " . DB_PREFIX . "blog_categories WHERE category_id = '" . (int)$id . "' ");

		 // $this->db->table('category_tags')->where('category_id', $id)->delete();
		 $this->db->query("DELETE FROM " . DB_PREFIX . "category_tags WHERE category_id = '" . (int)$id . "' ");

		 $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'blog_category_id=" . (int)$id . "'");

	}

	public function get_Comments($start, $limit)

	{


		$sql = "SELECT * FROM " . DB_PREFIX . "blog_article_comments bac LEFT JOIN " . DB_PREFIX . "blog_articles ba ON (bac.comment_article_id = ba.article_id) "; 

		$sql .= " ORDER BY  comment_id DESC";

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

		

		// return $this->db->query('blog_article_comments bac')->limit($limit)->skip($start)->join('blog_articles ba', 'bac.comment_article_id', 'ba.article_id')->sortBy('comment_id', 'desc')->get(['bac.comment_id', 'bac.comment_article_id','bac.comment_article_id', 'bac.customer_name','bac.comment', 'bac.status','ba.article_id', 'ba.title']);

	}

	public function deleteComment($comment_id)

	{

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_article_comments WHERE comment_id = '" . (int)$comment_id . "' ");

		 // $this->db->table('blog_article_comments')->where('comment_id', $comment_id)->delete();

		$this->db->query("DELETE FROM " . DB_PREFIX . "blog_article_comments WHERE comment_parent_id = '" . (int)$comment_id . "' ");

		// return $this->db->table('blog_article_comments')->where('comment_parent_id', $comment_id)->delete();

	}

	public function getCommentsTotalCount(){

		$query = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "blog_article_comments ");

		return $query->row['total'];



		// return $this->db->table('blog_article_comments')->count();

	}

	public function updateComment($data , $id)

	{

		$this->db->query("UPDATE " . DB_PREFIX . "blog_article_comments SET comment = '" . $this->db->escape($data['comment']) . "' WHERE comment_id = '".(int)$id."' ");

		// return $this->db->table('blog_article_comments')->find($id)->set($data);

	}

	public function updateCommentStatus($data, $id)

	{

		$this->db->query("UPDATE " . DB_PREFIX . "blog_article_comments SET status = '" . $this->db->escape($data['status']) . "' WHERE comment_id = '".(int)$id."' ");

		// return $this->db->table('blog_article_comments')->find($id)->set($data);

	}

	public function searchArticles($data,$start,$limit)

		{

			return $this->db->query("SELECT * FROM ".DB_PREFIX. "blog_articles WHERE title LIKE '%$data%' OR description LIKE '%$data%' LIMIT $start, $limit ")->rows;

		}

	public function getArticle($id)

	{

		$query = $this->db->query("SELECT * FROM ".DB_PREFIX. "blog_articles WHERE article_id = '". (int)$id ."' ");
		return $query->row;

		// return $this->db->table('blog_articles')->find($id)->get();

	}

	public function getProducts($data,$start,$limit)

		{

			return $this->db->query("SELECT * FROM ".DB_PREFIX. "product_description WHERE name LIKE '%$data%' OR description LIKE '%$data%' LIMIT $start, $limit ")->rows;

		}

	public function saveArticleTag($data)

	{

		$this->db->query("INSERT INTO " . DB_PREFIX . "article_tags SET tag_article_id = '" . $this->db->escape($data['tag_article_id']) . "', tag = '" . $this->db->escape($data['tag']) . "'");

	// return $this->db->table('article_tags')->add($data);

	}

	public function saveCategoryTag($data)

	{

		$this->db->query("INSERT INTO " . DB_PREFIX . "category_tags SET category_id = '" . $this->db->escape($data['category_id']) . "', category_tags = '" . $this->db->escape($data['category_tags']) . "'");

	// return $this->db->table('category_tags')->add($data);

	}

	public function getAutoTags($filter, $start, $limit)

	{

		return $this->db->query("SELECT * FROM ".DB_PREFIX."article_tags WHERE tag LIKE '%$filter%' GROUP BY tag LIMIT $start, $limit ")->rows;

	}

	public function getArticleTags($id)

	{

		$query = $this->db->query("SELECT tag FROM ".DB_PREFIX. "article_tags WHERE tag_article_id = '". (int)$id ."' ");
		return $query->row['tag'];

	// return $this->db->table('article_tags')->where('tag_article_id', $id)->get('tag');

	}

	public function getArticleTagsList($id)

	{

		$query = $this->db->query("SELECT * FROM " .DB_PREFIX. "article_tags WHERE tag_article_id = '". (int)$id ."'");
		return $query->rows;

	// return $this->db->table('article_tags')->where('tag_article_id', $id)->get();

	}

	public function getArticleCategories($id)

	{

		$query = $this->db->query("SELECT * FROM " .DB_PREFIX. "blog_article_category ac LEFT JOIN " .DB_PREFIX. "blog_categories c ON (ac.blog_category_id = c.category_id)  WHERE blog_article_id = '". (int)$id ."'");
		return $query->rows;

	// return $this->db->table('blog_article_category ac')->join('blog_categories c','ac.blog_category_id', 'c.category_id')->where('blog_article_id', $id)->get();

	}

	public function getArticleCategoriesList($id)

	{

		return $this->db->query("SELECT c.name FROM ".DB_PREFIX."blog_categories  AS c LEFT JOIN ".DB_PREFIX."blog_article_category AS ac ON ac.blog_category_id = c.category_id WHERE ac.blog_article_id = $id")->rows;

	}

	public function getCategoryTags($id)

	{

		$query = $this->db->query("SELECT * FROM " .DB_PREFIX. "category_tags WHERE category_id = '". (int)$id ."' ");
		return $query->rows;

	// return $this->db->table('category_tags')->where('category_id', $id)->get();

	}

	public function deleteArticleTags($id)

	{

		$this->db->query("DELETE FROM " . DB_PREFIX . "article_tags WHERE tag_article_id = '" . (int)$id . "' ");

	// return $this->db->table('article_tags')->where('tag_article_id', $id)->delete();

	}

	public function deleteCategoryTags($id)

	{

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_tags WHERE category_id = '" . (int)$id . "' ");

	// return $this->db->table('category_tags')->where('category_id', $id)->delete();

	}
}