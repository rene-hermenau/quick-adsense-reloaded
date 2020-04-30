import React, { Component, Fragment } from 'react';

import './QuadsAdTargeting.scss';
import QuadsUserTargeting from  '../../common/user-targeting/QuadsUserTargeting'
import QuadsVisibility from  '../../common/visibility/QuadsVisibility'
import QuadsAdvancePosition from  '../../common/advance-position/QuadsAdvancePosition'
import queryString from 'query-string'


class QuadsAdTargeting extends Component {

  constructor(props) {
    super(props);    
    this.state = {               
    };       
  } 
 
  render() {

    const {__} = wp.i18n;    

    const page = queryString.parse(window.location.search);            
          return (
                <div>
                <div className="quads-settings-group">
                <div>{__('Position', 'quick-adsense-reloaded')}</div>  
                <div className="quads-panel">
                <div className="quads-panel-body"> 
                <table>
                  <tbody>
                    <tr>
                    <td><label>{__('Where will the AD appear?', 'quick-adsense-reloaded')}</label></td>
                    <td><QuadsAdvancePosition parentState={this.props.parentState} adFormChangeHandler = {this.props.adFormChangeHandler}/></td>  
                    </tr>
                  </tbody>
                </table>                                 
                </div>  
                </div> 
                </div>                
                <QuadsVisibility 
                  parentState                  ={this.props.parentState} 
                  updateVisibility             ={this.props.updateVisibility}
                />
                {quads_localize_data.is_pro ? <QuadsUserTargeting 
                  parentState                  ={this.props.parentState} 
                  updateVisitorTarget          ={this.props.updateVisitorTarget}
                />:''}                
              <div class="quads-btn-navigate">
                <div className="quads-next"><a onClick={this.props.publish} className="quads-btn quads-btn-primary">{page.action == 'edit' ? 'Update' : 'Publish'}</a></div>
                <div className=""><a onClick={this.props.movePrev} className="quads-btn quads-btn-primary">{__('Prev', 'quick-adsense-reloaded')}</a></div>
                </div>
              </div>
            );
  }
}

export default QuadsAdTargeting;