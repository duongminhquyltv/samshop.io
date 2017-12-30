<?php
Class MY_Controller extends CI_Controller
{
    //bien gui du lieu sang ben view
    public $data = array();
    
    function __construct()
    {
        //ke thua tu CI_Controller
        parent::__construct();
        
        $controller = $this->uri->segment(1);
        switch ($controller)
        {
            case 'admin' :
                {
                    //xu ly cac du lieu khi truy cap vao trang admin
                    $this->load->helper('admin');
                    $this->_check_login();
                    break;
                }
            default:
                {
                    $this->load->view('site/user/login');
                    $this->load->view('site/user/register');
                    $this->load->library('cart');

                    $carts = $this->cart->contents();
                    $this->data['cartslist'] = $carts;
                    //tong so san pham co trong gio hang
                    $total_items = $this->cart->total_items();
                    $this->data['$total_item'] = $total_items;
                    if($total_items <= 0)
                    {
                        //redirect();
                    }
                    //tong so tien can thanh toan
                    $total_amount = 0;
                    foreach ($carts as $row)
                    {
                        $total_amount = $total_amount + $row['subtotal'];  
                    }
                    $this->data['total_amounts'] = $total_amount;

                    //xu ly du lieu o trang ngoai
                    //lay danh sach danh muc san pham
                    $this->load->model('catalog_model');
                    $input = array();
                    $input['where'] = array('parent_id' => 0);
                    $catalog_list = $this->catalog_model->get_list($input);
                    foreach ($catalog_list as $row)
                    {
                        $input['where'] = array('parent_id' => $row->id);
                        $subs = $this->catalog_model->get_list($input);
                        $row->subs = $subs;
                    }
                    $this->data['catalog_list'] = $catalog_list;
                    
                    //lay danh sach bai viet moi
                    $this->load->model('news_model');
                    $input = array();
                    $input['limit'] = array(5, 0);
                    $news_list = $this->news_model->get_list($input);
                    $this->data['news_list'] = $news_list;
                    
                    
                    //kiem tra xem thanh vien da dang nhap hay chua
                    $user_id_login = $this->session->userdata('user_id_login');
                    $this->data['user_id_login'] = $user_id_login;
                    //neu da dang nhap thi lay thong tin cua thanh vien
                    if($user_id_login)
                    {
                        $this->load->model('user_model');
                        $user_info = $this->user_model->get_info($user_id_login);
                        $this->data['user_info'] = $user_info;
                    }
                    
                    //goi toi thu vien
                    
                    $this->data['total_items']  = $this->cart->total_items();
                }
            
        }
    }
    
    /*
     * Kiem tra trang thai dang nhap cua admin
     */
    private function _check_login()
    {
        $controller = $this->uri->rsegment('1');
        $controller = strtolower($controller);
    
        $login = $this->session->userdata('login');
        //neu ma chua dang nhap,ma truy cap 1 controller khac login
        if(!$login && $controller != 'login')
        {
            redirect(admin_url('login'));
        }
        //neu ma admin da dang nhap thi khong cho phep vao trang login nua.
        if($login && $controller == 'login')
        {
            redirect(admin_url('home'));
        }
    }
    
    /*
     * Lấy danh sách đệ quy theo mãng. 
     * @param: 
     *        1. $array_recursive: Mảng đệ qui
     *        2. $parent_id      : Khóa cha dùng để đệ qui.
     *        3. $output         : Đầu ra.
     */
    public function _recursive($parent_id = 0,$array_recursive,$char = '',&$output = null){        
        if(isset($array_recursive) && is_array($array_recursive)){            
            foreach ($array_recursive as $key => $item)
            {        
                // Nếu là chuyên mục con thì hiển thị
                if ($item->parent_id == $parent_id)
                {
                    $output .= '<option value="'.$item->id.'">'.$char.$item->title.'</option>';
                    // Xóa chuyên mục đã lặp
                    unset($array_recursive[$key]);
                    
                    // Tiếp tục đệ quy để tìm chuyên mục con của chuyên mục đang lặp
                    $this->_recursive($item->id,$array_recursive, $char.'---',$output);
                }
            }
            return $output;
        }
    }

    function getURLCategory( $array_category, $path = '/catalog/view', $id , &$array_return = array())
    {
        if(isset($id) && is_array($array_category)){
            foreach($array_category as $key => $item)
            {
                ///nếu có chuyên mục cha thì hiển thị.                
                if($item->id == $id && $item->parent_id >= 0){
                    $array_return[$item->id] = $item->name;                    
                    $this->getURLCategory( $array_category , $path , $item->parent_id , $array_return );
                }
            }        

            $array_return = array_reverse($array_return,true);

            foreach ($array_return as $key => $value) {
                $this->breadcrumbs->push($value,$path.$key);
            }
            return true;           
        }
    } 
    
    /*
     * Lấy danh sách menu.
     * @param:  
     *          1. array_recursive: danh sách mảng menu.
     *          2. $output        : đầu ra. 
     */
    // BƯỚC 2: HÀM ĐỆ QUY HIỂN THỊ CATEGORIES
    function _menu($parent_id = 0, $array_recursive,  $char = '',$class = 'cssmenu', $stt, &$output)
    {
        // BƯỚC 2.1: LẤY DANH SÁCH CATE CON
        $cate_child = array();
        foreach ($array_recursive as $key => $item)
        {
            // Nếu là chuyên mục con thì hiển thị
            if ($item->parent_id == $parent_id)
            {
                $cate_child[] = $item;
                unset($array_recursive[$key]);
            }
        }
        
        // BƯỚC 2.2: HIỂN THỊ DANH SÁCH CHUYÊN MỤC CON NẾU CÓ        
        if ($cate_child)
        {
            if ($stt == 0){
                $class = 'cssmenu';
            }
            else if ($stt == 1){
                $class = 'submenu';
            }
            else if ($stt == 2){
                $class = 'sub-sub-menu';
            }
            $output .='<ul>';
            foreach ($cate_child as $key => $item)
            {
                // Hiển thị tiêu đề chuyên mục
                $output .= '<li><a href="'.$item->url.'" title="'.$item->title.'">'.$item->title.'</a>';
                // Tiếp tục đệ quy để tìm chuyên mục con của chuyên mục đang lặp
    
                $this->_menu($item->id, $array_recursive, $char.'|---',$class, ++$stt, $output);
                $output .= '</li>';
            }
            $output .= '</ul>';
        }
        return $output;
    }
    /*
     * Lay danh sach chuyen muc cha.
     * @param : 
     */
    function _get_parent_category( $id , $array_recursive , $output ){
        if(isset($id) && intval($id) && isset($array_recursive)){
            foreach ($array_recursive as $key => $item)
            {
                // Nếu là chuyên mục con thì hiển thị
                if ($item->parent_id == $id)
                {
                    $output .= '<option value="'.$item->id.'">'.$char.$item->title.'</option>';
                    // Xóa chuyên mục đã lặp
                    unset($array_recursive[$key]);
                    
                    // Tiếp tục đệ quy để tìm chuyên mục con của chuyên mục đang lặp
                    $this->_recursive($item->id,$array_recursive, $char.'---',$output);
                }
            }
            return $output;
        }        
    }
}


