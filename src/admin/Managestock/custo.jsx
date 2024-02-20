import React from "react";
import "./custom.css";
const Custo = () => {
  return (
    <>
      <div className="custom-table-main">
        <div className="custom-container custom-container-row">
          <div className="custom-container-inner-div left">
            <div className="cell div-expand-icon">icons</div>
            <div className="cell div-img">Image</div>
            <div className="cell div-name">Name</div>
          </div>
          <div className="custom-container-inner-div right">
            <div className="cell div-sku">SKU</div>
            <div className="cell div-product-type">Type</div>
            <div className="cell div-regular-price">Regular price</div>
            <div className="cell div-sale-price">Sale price</div>
            <div className="cell div-manage-stock">Manage stock</div>
            <div className="cell div-stock-status">Stock status</div>
            <div className="cell div-backorder">Back orders</div>
            <div className="cell div-stock">Stock</div>
            <div className="cell div-subcriberno">Subscriber No.</div>
            <div className="master-edit-btn">
            <svg xmlns="http://www.w3.org/2000/svg"><path d="M12 15a1 1 0 0 1-.707-.293l-4-4a1 1 0 1 1 1.414-1.414L12 12.586l3.293-3.293a1 1 0 0 1 1.414 1.414l-4 4A1 1 0 0 1 12 15z"/></svg>
            </div>
          </div>
        </div>
        <div className="custom-container custom-container-col">
          <div className="custom-container-inner-div left">
            <div className="cell div-expand-icon">
              <button className="variable-product-expand">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="currentColor"
                  class="bi bi-arrow-right-short"
                  viewBox="0 0 16 16"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8"
                  />
                </svg>
              </button>
            </div>
            <div className="cell div-img">
              <img
                className="div-img-img"
                src="https://images.unsplash.com/photo-1682687218982-6c508299e107?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDF8MHxlZGl0b3JpYWwtZmVlZHwxfHx8ZW58MHx8fHx8"
                alt=""
              />
            </div>
            <div className="cell div-name">
              <input type="text" />
              <span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 16 16"
                  id="edit"
                >
                  <path
                    fill="#212121"
                    d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"
                  ></path>
                </svg>
              </span>
            </div>
          </div>
          <div className="custom-container-inner-div right">
            <div className="cell div-sku">
                <p>Bishal</p>
              <input type="text" />
              <span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 16 16"
                  id="edit"
                >
                  <path
                    fill="#212121"
                    d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"
                  ></path>
                </svg>
              </span>
            </div>
            <div className="cell div-product-type"><p>Bishal</p>Simple</div>
            <div className="cell div-regular-price">
            <p>Bishal</p>
              <input type="text" />
              <span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 16 16"
                  id="edit"
                >
                  <path
                    fill="#212121"
                    d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"
                  ></path>
                </svg>
              </span>
            </div>
            <div className="cell div-sale-price">
            <p>Bishal</p>
              <input type="text" />
              <span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 16 16"
                  id="edit"
                >
                  <path
                    fill="#212121"
                    d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"
                  ></path>
                </svg>
              </span>
            </div>
            <div className="cell div-manage-stock">
            <p>Bishal</p>
              <div className="div-manage-stock-container">
                <div className="custome-toggle-default">
                  <input type="checkbox" />
                  <label></label>
                </div>
              </div>
            </div>
            <div className="cell div-stock-status"><p>Bishal</p>Out of stock</div>
            <div className="cell div-backorder"><p>Bishal</p>No</div>
            <div className="cell div-stock">
            <p>Bishal</p>
              <input type="text" />
              <span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 16 16"
                  id="edit"
                >
                  <path
                    fill="#212121"
                    d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"
                  ></path>
                </svg>
              </span>
            </div>
            <div className="cell div-subcriberno"><p>Bishal</p>1000</div>
          </div>
        </div>
      </div>
    </>
  );
};
export default Custo;