import React, { Component, Fragment } from 'react';
import './QuadsAdConfigFields.scss';
import QuadsAdModal from '../../common/modal/QuadsAdModal';
import Icon from '@material-ui/core/Icon';
import Select from "react-select";

class QuadsAdConfigFields extends Component {
  constructor(props) {
    super(props);    
    this.state = { 
    adsToggle : false,    
    random_ads_list:[],  
    getallads_data: [],
    currentselectedvalue: "",
    currentselectedlabel : "",              
    };       
  }   
  adsToggle = () => {
  
  this.setState({adsToggle:!this.state.adsToggle,currentselectedvalue : ''});
}
addIncluded = (e) => {

    e.preventDefault();  

    let type  = this.state.multiTypeLeftIncludedValue;
    let value = this.state.multiTypeRightIncludedValue;
  
    if( typeof (value.value) !== 'undefined'){
      const {random_ads_list} = this.state;
      let data    = random_ads_list;
      data.push({type: type, value: value});
      let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);          
      this.setState({random_ads_list: newData});       
    }        
  
}

  static getDerivedStateFromProps(props, state) {    

    if(!state.adsToggle){
      return {
        random_ads_list: props.parentState.quads_post_meta.random_ads_list, 
      };
    }else{
      return null;
    }
    
  }
    componentDidUpdate (){
    
    const random_ads_list = this.state.random_ads_list; 
    if(random_ads_list &&random_ads_list.length > 0 ){
      this.props.updateRandomAds(random_ads_list);
    }
    
  }
  
  selectimages  = (event) => {
      var image_frame;

      var self =this;
      if(image_frame){
       image_frame.open();
      }

      // Define image_frame as wp.media object
      image_frame = wp.media({
                 library : {
                      type : 'image',
                  }
             });
      image_frame.on('close',function() {
                  // On close, get selections and save to the hidden input
                  // plus other AJAX stuff to refresh the image preview
                  var selection =  image_frame.state().get('selection');
                  var id = '';
                  var src = '';
                  var my_index = 0;
                  selection.each(function(attachment) {
                     id = attachment['id'];
                     src = attachment.attributes.sizes.full.url;
                  });
                  self.props.adFormChangeHandler({ target : { name : 'image_src_id' , value : id } });
                  self.props.adFormChangeHandler({ target : { name : 'image_src' , value : src } });                  
               });   
      image_frame.on('open',function() {
              // On open, get the id from the hidden input
              // and select the appropiate images in the media manager
              var selection =  image_frame.state().get('selection');

            });
          image_frame.open();

    }
    remove_image = (e) => {
    this.props.adFormChangeHandler({ target : { name : 'image_src_id' , value : '' } });
    this.props.adFormChangeHandler({ target : { name : 'image_src' , value : '' } });    

}
removeSeleted = (e) => {
      let index = e.currentTarget.dataset.index;  
      const { random_ads_list } = { ...this.state };    
      random_ads_list.splice(index,1);
      this.setState(random_ads_list);

}
  getallads = (search_text = '',page = '') => {
   let url = quads_localize_data.rest_url + "quads-route/get-ads-list?posts_per_page=100&page="+page;
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': quads_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {      
          let getallads_data =[];
          Object.entries(result.posts_data).map(([key, value]) => {
          if(value.post_meta['ad_type'] != "random_ads" && value.post['post_status'] != "draft")
            getallads_data.push({label: value.post['post_title'], value: value.post['post_id']});
          })      
            this.setState({
            isLoaded: true,
            getallads_data: getallads_data,
          });
          
        },        
        (error) => {
          this.setState({
             isLoaded: true,         
          });
        }
      );          
  }

  addselected = (e) => {

    e.preventDefault();  

    let value  = this.state.currentselectedvalue;  
    let label  = this.state.currentselectedlabel;  
  
    if( typeof (value) !== 'undefined' && value != ''){
      const {random_ads_list} = this.state;
      let data    = random_ads_list;
      data.push({ value: value,label: label});
      let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);          
      this.setState({random_ads_list: newData,adsToggle : false});    
         
    }        
  
}
   componentDidMount() {  
          this.getallads(); 
  } 
    selectAdchange = (option) => {    
   
      this.setState({currentselectedlabel: option.label,currentselectedvalue: option.value});

  }
  render() {     

          const {__} = wp.i18n;
          const post_meta = this.props.parentState.quads_post_meta;
          const show_form_error = this.props.parentState.show_form_error;
          const comp_html = [];   
          let ad_type_name = '';     

          switch (this.props.ad_type) {

            case 'adsense':
             ad_type_name = 'AdSense';  
              comp_html.push(<div key="adsense">
                <table>
                  <tbody>
                    <tr><td><label>{__('Data Client ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.g_data_ad_client == '') ? 'quads_form_error' : ''} value={post_meta.g_data_ad_client} placeholder="ca-pub-2005XXXXXXXXX342" onChange={this.props.adFormChangeHandler} type="text" id="g_data_ad_client" name="g_data_ad_client" />
                    {(show_form_error && post_meta.g_data_ad_client == '') ? <div className="quads_form_msg"><span className="material-icons">
error_outline</span>Enter Data Client ID</div> :''} </td></tr>
                    <tr><td><label>{__('Data Slot ID', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.g_data_ad_slot == '') ? 'quads_form_error' : ''}  value={post_meta.g_data_ad_slot} onChange={this.props.adFormChangeHandler} type="text" id="g_data_ad_slot" name="g_data_ad_slot" placeholder="70XXXXXX12" />
                    {(show_form_error && post_meta.g_data_ad_slot == '') ? <div className="quads_form_msg"><span className="material-icons">
error_outline
</span>Enter Data Slot ID</div> :''}</td></tr>
                    <tr><td><label>{__('Size', 'quick-adsense-reloaded')}</label></td><td>
                      <div>
                        <select value={post_meta.adsense_type} onChange={this.props.adFormChangeHandler} name="adsense_type" id="adsense_type">
                        <option value="normal">{__('Fixed Size', 'quick-adsense-reloaded')}</option>
                        <option value="responsive">{__('Responsive', 'quick-adsense-reloaded')}</option> 
                      </select>
                      {
                        post_meta.adsense_type !== 'responsive' ?                        
                      <div className="quads-adsense-width-heigth">
                        
                        <div className="quads-adsense-width">
                          <label>{__('Width', 'quick-adsense-reloaded')}
                          <input value={post_meta.g_data_ad_width ? post_meta.g_data_ad_width:'300'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_width" name="g_data_ad_width" /> 
                          </label>
                        </div>
                        <div className="quads-adsense-height">
                          <label>{__('Height', 'quick-adsense-reloaded')}
                          <input value={post_meta.g_data_ad_height  ? post_meta.g_data_ad_height:'250'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_height" name="g_data_ad_height" />  
                          </label>
                        </div>
                      </div>
                      : ''
                      }
                      </div>
                      </td></tr>
                  </tbody>
                </table>
                </div>);

              break;
          
              case 'plain_text':                
                ad_type_name = 'Plain Text / HTML / JS';
                comp_html.push(<div key="plain_text">
                  <table><tbody>
                  <tr>
                  <td><label>{__('Plain Text / HTML / JS', 'quick-adsense-reloaded')}</label></td> 
                  <td><textarea className={(show_form_error && post_meta.code == '') ? 'quads_form_error' : ''}  cols="50" rows="5" value={post_meta.code} onChange={this.props.adFormChangeHandler} id="code" name="code" />
                  {(show_form_error && post_meta.code == '') ? <div className="quads_form_msg"><span className="material-icons">error_outline</span>Enter Plain Text / HTML / JS</div> : ''}</td>
                  </tr>
                  </tbody></table>
                  </div>);      
              break; 
               case 'random_ads':                
                 ad_type_name = 'Random Ads';
                comp_html.push(<div key="random_ads" className="quads-user-targeting"> 
       <h2>Select Ads<a onClick={this.adsToggle}><Icon>add_circle</Icon></a>  </h2>

                
             <div className="quads-target-item-list">
              {                
              this.state.random_ads_list ? 
              this.state.random_ads_list.map( (item, index) => (
                <div key={index} className="quads-target-item">
                  <span className="quads-target-label">{item.label}</span>
                  <span className="quads-target-icon" onClick={this.removeSeleted} data-index={index}><Icon>close</Icon></span> 
                </div>
               ) )
              :''}
              <div>{ (this.state.random_ads_list.length <= 0 && show_form_error) ? <span className="quads-error"><div className="quads_form_msg"><span className="material-icons">error_outline</span>Select at least one Ad</div></span> : ''}</div>
             </div>             
        

        {this.state.adsToggle ?
        <div className="quads-targeting-selection">
        <table className="form-table">
         <tbody>
           <tr>             
           <td>
            <Select              
              name="userTargetingIncludedType"
              placeholder="Select Ads"              
              options= {this.state.getallads_data}
              value  = {this.multiTypeLeftIncludedValue}
              onChange={this.selectAdchange}                                                 
            />             
           </td>
           <td><a onClick={this.addselected} className="quads-btn quads-btn-primary">Add</a></td>
           </tr>
         </tbody> 
        </table>
        </div>
        : ''}
       </div>);      
              break; 
            case 'double_click':
             ad_type_name = 'Google AD Manager (DFP)';  
              comp_html.push(<div key="double_click">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Network Code', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.network_code == '') ? 'quads_form_error' : ''} value={post_meta.network_code} onChange={this.props.adFormChangeHandler} type="text" id="network_code" name="network_code" placeholder="Network Code" />
                    {(show_form_error && post_meta.network_code == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Network Code</div> :''}
                     </td></tr>
                    <tr><td><label>{__('AD Unit Name', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.ad_unit_name == '') ? 'quads_form_error' : ''}  value={post_meta.ad_unit_name} onChange={this.props.adFormChangeHandler} type="text" placeholder="AD Unit Name" id="ad_unit_name" name="ad_unit_name" />
                    {(show_form_error && post_meta.ad_unit_name == '') ? <div className="quads_form_msg"><span className="material-icons">
error_outline
</span>Enter AD Unit Name</div> :''}</td></tr>
                    <tr><td><label>{__('Size', 'quick-adsense-reloaded')}</label></td><td>
                      <div>
                        <select value={post_meta.adsense_type} onChange={this.props.adFormChangeHandler} name="adsense_type" id="adsense_type">
                        <option value="normal">{__('Fixed Size', 'quick-adsense-reloaded')}</option>
                        <option value="responsive">{__('Responsive', 'quick-adsense-reloaded')}</option> 
                      </select>
                      {
                        post_meta.adsense_type !== 'responsive' ?                        
                      <div className="quads-adsense-width-heigth">
                        
                        <div className="quads-adsense-width">
                          <label>{__('Width', 'quick-adsense-reloaded')}
                          <input value={post_meta.g_data_ad_width ? post_meta.g_data_ad_width:'300'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_width" name="g_data_ad_width" /> 
                          </label>
                        </div>
                        <div className="quads-adsense-height">
                          <label>{__('Height', 'quick-adsense-reloaded')}
                          <input value={post_meta.g_data_ad_height  ? post_meta.g_data_ad_height:'250'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_height" name="g_data_ad_height" />  
                          </label>
                        </div>
                      </div>
                      : ''
                      }
                      </div>
                      </td></tr>
                  </tbody>
                </table>
                </div>);

              break;
            case 'yandex':
             ad_type_name = 'Yandex';  
              comp_html.push(<div key="yandex">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Block Id', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.block_id == '') ? 'quads_form_error' : ''} value={post_meta.block_id} onChange={this.props.adFormChangeHandler} type="text" id="block_id" name="block_id" placeholder="Block Id" />
                    {(show_form_error && post_meta.block_id == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Block Id</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);

              break;
                           case 'mgid':
             ad_type_name = 'MGID';  
              comp_html.push(<div key="mgid">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Data Publisher', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_publisher == '') ? 'quads_form_error' : ''} value={post_meta.data_publisher} onChange={this.props.adFormChangeHandler} type="text" id="data_publisher" name="data_publisher" placeholder="site.com" />
                    {(show_form_error && post_meta.data_publisher == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Data Publisher</div> :''}
                     </td></tr>
                           <tr><td>
                    <label>{__('Data Widget', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_widget == '') ? 'quads_form_error' : ''} value={post_meta.data_widget} onChange={this.props.adFormChangeHandler} type="text" id="data_widget" name="data_widget" placeholder="123456" />
                    {(show_form_error && post_meta.data_widget == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Data Widget</div> :''}
                     </td></tr>
                           <tr><td>
                    <label>{__('Data Container', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_container == '') ? 'quads_form_error' : ''} value={post_meta.data_container} onChange={this.props.adFormChangeHandler} type="text" id="data_container" name="data_container" placeholder="M87ScriptRootC123645" />
                    {(show_form_error && post_meta.data_container == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Data Container</div> :''}
                     </td></tr>
                           <tr><td>
                    <label>{__('Data Js Src', 'quick-adsense-reloaded')}</label></td><td><input className={(show_form_error && post_meta.data_js_src == '') ? 'quads_form_error' : ''} value={post_meta.data_js_src} onChange={this.props.adFormChangeHandler} type="text" id="data_js_src" name="data_js_src" placeholder="//jsc.mgid.com/a/m/quads.com.123645.js" />
                    {(show_form_error && post_meta.data_js_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Data Js Src</div> :''}
                     </td></tr>
                     <tr><td><label>{__('Size', 'quick-adsense-reloaded')}</label></td><td>
                      <div>
                        <select value={post_meta.adsense_type} onChange={this.props.adFormChangeHandler} name="adsense_type" id="adsense_type">
                        <option value="normal">{__('Fixed Size', 'quick-adsense-reloaded')}</option>
                        <option value="responsive">{__('Responsive', 'quick-adsense-reloaded')}</option> 
                      </select>
                      {
                        post_meta.adsense_type !== 'responsive' ?                        
                      <div className="quads-adsense-width-heigth">
                        
                        <div className="quads-adsense-width">
                          <label>{__('Width', 'quick-adsense-reloaded')}
                          <input value={post_meta.g_data_ad_width ? post_meta.g_data_ad_width:'300'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_width" name="g_data_ad_width" /> 
                          </label>
                        </div>
                        <div className="quads-adsense-height">
                          <label>{__('Height', 'quick-adsense-reloaded')}
                          <input value={post_meta.g_data_ad_height  ? post_meta.g_data_ad_height:'250'} onChange={this.props.adFormChangeHandler} type="number" id="g_data_ad_height" name="g_data_ad_height" />  
                          </label>
                        </div>
                      </div>
                      : ''
                      }
                      </div>
                      </td></tr>
                  </tbody>
                </table>
                </div>);
              break;
            case 'ad_image':
             ad_type_name = 'Banner';  
              comp_html.push(<div key="ad_image">
                <table>
                  <tbody>
                    <tr><td>
                    <label>{__('Upload Ad Banner', 'quick-adsense-reloaded')}</label></td><td>
                   {post_meta.image_src == '' ? <div><a className="button" onClick={this.selectimages}>{__(' Upload Banner', 'quick-adsense-reloaded')}</a></div>
                   : <div>
                   <img src={post_meta.image_src} className="banner_image" />
                   <a className="button" onClick={this.remove_image}>{__('Remove Banner', 'quick-adsense-reloaded')}</a></div>}
                     
                      
                    {(show_form_error && post_meta.image_src == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Upload Ad Image</div> :''}
                     </td></tr>
                     <tr><td>
                    <label>{__('Ad Anchor link', 'quick-adsense-reloaded')}</label></td><td>
                    <input value={post_meta.image_redirect_url} onChange={this.props.adFormChangeHandler} type="text" id="image_redirect_url" name="image_redirect_url" placeholder="Ad Anchor link" />
                    {(show_form_error && post_meta.image_redirect_url == '') ? <div className="quads_form_msg"><span className="material-icons">
                    error_outline</span>Enter Ad Anchor link</div> :''}
                     </td></tr>
                  </tbody>
                </table>
                </div>);

              break;


            default:
              comp_html.push(<div key="noads" >{__('Ad not found', 'quick-adsense-reloaded')}</div>);
              break;
          }
              return(
                <div>{ad_type_name} {__('Ad Configuration', 'quick-adsense-reloaded')}
                {this.props.ad_type == 'adsense' ? 
                <div className="quads-autofill-div"><a className="quads-autofill" onClick={this.props.openModal}>{__('Autofill', 'quick-adsense-reloaded')}</a>
                <a className="quads-general-helper" target="_blank" href="https://wpquads.com/documentation/how-to-find-data-client-id-data-slot-id-for-adsense-integration/"></a>
                <QuadsAdModal 
                 closeModal    = {this.props.closeModal}
                 parentState={this.props.parentState} 
                 title={__('Enter AdSense text and display ad code here', 'quick-adsense-reloaded')}
                 description={__('Do not enter AdSense page level ads or Auto ads! Learn how to create AdSense ad code', 'quick-adsense-reloaded')}  
                  content={
                    <div>
                      <div><textarea className="quads-auto-fill-textarea" cols="80" rows="15" onChange={this.props.modalValue} value={this.props.quads_modal_value}/></div>
                      <div><a className="button" onClick={this.props.getAdsenseCode}>{__('Get Code', 'quick-adsense-reloaded')}</a></div>
                    </div>
                  }/>
                </div> : ''}
                <div className="quads-panel">
                 <div className="quads-panel-body">{comp_html}</div>
              </div>
              </div>
              );
  }
}

export default QuadsAdConfigFields;